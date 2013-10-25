<?php

namespace Atipik\Hoa\WebSocketBundle\WebSocket;

use Atipik\Hoa\WebSocketBundle\Log\Logger;
use Hoa\Websocket\Server as HoaWebSocketServer;

/**
 * Server
 */
class Server extends HoaWebSocketServer
{
    protected $logger;

    /**
     * Returns current logger
     *
     * @return Atipik/Hoa/WebSocketBundle/Log/Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set current logger
     *
     * @param Atipik/Hoa/WebSocketBundle/Log/Logger $logger
     *
     * @return self
     */
    public function setLogger(Logger $logger = null)
    {
        $this->logger = $logger;

        return $this;
    }
}