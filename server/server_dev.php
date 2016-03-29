<?php

use React\Http\Handler\RequestHandler;
use React\Http\Processor\FormField;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

$loader = require __DIR__.'/../app/autoload.php';

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
//$kernel->loadClassCache();
$kernel->boot();

$http = $kernel->getContainer()->get('http_server');
$loop = $http->getLoop();
$socket = $http->getSocket();

// Warm up container
$kernel->handle(Request::create('/'));

$requestsQuantity = 0;

$http->on('request', function (\React\Http\Request $request, \React\Http\Response $response) use ($kernel, &$requestsQuantity) {

    // Clean memory leaks
    if (++$requestsQuantity % 5 === 0) {
        gc_collect_cycles();
    }

    $internalRequest = \CommonBundle\HttpFoundation\Request::createFromReactRequest($request);

    $requestHandler = new RequestHandler();

    $requestHandler->on('end', function () use ($kernel, $internalRequest, $response) {
        $internalResponse = $kernel->handle($internalRequest);

        // Collect all headers and cookies
        $headers = $internalResponse->headers->all();
        foreach ($internalResponse->headers->getCookies() as $cookie) {
            $headers['Set-Cookie'][] = $cookie;
        }

        $response->writeHead($internalResponse->getStatusCode(), $headers);
        $response->end($internalResponse->getContent());
        $kernel->terminate($internalRequest, $internalResponse);

        $kernel->getContainer()->get('doctrine.orm.entity_manager')->clear();

        $kernel->getContainer()->set('form.type.entity', null);
        $kernel->getContainer()->set('form.registry', null);
        $kernel->getContainer()->set('form.resolved_type_factory', null);
        $kernel->getContainer()->set('form.factory', null);

        $kernel->getContainer()->set('request', null);
        $kernel->getContainer()->set('session', null);
        $kernel->getContainer()->set('session.storage', null);

        if (strpos($internalRequest->getRequestUri(), '/admin') !== false) {
            $kernel->getContainer()->set('sonata.admin.builder.orm_form', null);
            $kernel->getContainer()->set('sonata.admin.product', null);
//            $kernel->shutdown();
        }
    });

    $requestHandler->on('data', function (FormField $field) use ($internalRequest) {
        if ($field->isFile()) {
            $filename = tempnam(sys_get_temp_dir(), '');
            $dataHandler = function ($data) use ($filename) {
                file_put_contents($filename, $data, FILE_APPEND | FILE_BINARY);
            };
            $endDataHandler = function ($data) use ($filename, $field, $internalRequest, $dataHandler) {
                $dataHandler($data);
                if (!filesize($filename)) {
                    return;
                }
                $file = new UploadedFile(
                    $filename,
                    $field->attributes->get(FormField::ORIGINAL_FILENAME),
                    null, null, null, true
                );
                parse_str($field->getName(), $data);
                array_walk_recursive($data, function (&$item) use ($file) {
                    if ($item === '') {
                        $item = $file;
                    }
                });
                reset($data);
                $key = key($data);
                $data = $data[$key];
                if ($internalRequest->files->has($key)) {
                    $value = $internalRequest->files->get($key);
                    $data = array_merge_recursive($value, $data);
                }
                $internalRequest->files->set($key, $data);
            };
        } else {
            $total = '';
            $dataHandler = function ($data) use (&$total) {
                if (is_array($data)) {
                    $total = $data;
                } else {
                    $total .= $data;
                }
            };
            $endDataHandler = function ($data) use (&$total, $dataHandler, $internalRequest, $field) {
                $dataHandler($data);
                $value = $internalRequest->attributes->get($field->getName());
                if ($value === null) {
                    $internalRequest->request->set($field->getName(), $total);
                } elseif (is_array($value)) {
                    $value[] = $total;
                    $internalRequest->request->set($field->getName(), $value);
                } else {
                    $internalRequest->request->set($field->getName(), [$value, $total]);
                }
            };
        }
        $field->on('data', $dataHandler);
        $field->on('end', $endDataHandler);
    });

    $requestHandler->handle($request);
});

if (count($argv) > 1) {
    $port = (int) $argv[1];
} else {
    $port = 8080;
}

$socket->listen($port, '0.0.0.0');

$loop->run();