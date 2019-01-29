<?php


namespace SwooleBundle\Handler;


use Swoole\Http\Request;
use Swoole\Http\Response;
use SwooleBundle\Adapter\SymfonyRequestAdapter;
use Symfony\Component\HttpKernel\KernelInterface;

class RequestHandler
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param Request  $request
     * @param Response $response
     */
    public function handle(Request $request, Response $response)
    {
        try {

            $internalResponse = $this->kernel->handle(new SymfonyRequestAdapter($request));

            $response->status($internalResponse->getStatusCode());

            foreach ($internalResponse->headers->all() as $name => $value) {
                $response->header($name, $value);
            }

            $response->end($internalResponse->getContent());

        } catch (\Exception $exception) {

            $response->status(500);
            $response->end();

        }
    }
}