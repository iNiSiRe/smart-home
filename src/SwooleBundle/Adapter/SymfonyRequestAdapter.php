<?php


namespace SwooleBundle\Adapter;


use Symfony\Component\HttpFoundation\Request;

class SymfonyRequestAdapter extends Request
{
    public function __construct(\Swoole\Http\Request $request)
    {
        parent::__construct(
            $request->server['request_uri'],
            $request,
            [],
            [],
            [],
            [],
            $request->rawcontent()
        );
    }
}