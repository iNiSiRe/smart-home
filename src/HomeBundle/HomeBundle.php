<?php

namespace HomeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HomeBundle extends Bundle
{
    public function boot()
    {
        // Instancing WebSocketServer service
        $this->container->get('home.web_socket_server');
    }
}
