<?php

namespace Atipik\Hoa\WebSocketBundle\WebSocket\Module;

use Atipik\Hoa\WebSocketBundle\Log\Logger;
use Atipik\Hoa\WebSocketBundle\WebSocket\Runner;
use Hoa\Core\Event\Bucket;

/**
 * Module
 */
abstract class Module implements ModuleInterface
{
    protected $logger;
    protected $bucket;

    /**
     * Returns current bucket
     *
     * @return Bucket
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Returns group name
     *
     * @return string
     */
    public function getGroup()
    {
        return '';
    }

    /**
     * Returns current logger
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this
            ->logger
            ->setPrefix(get_class($this))
        ;
    }

    /**
     * Returns current node
     *
     * @return \Hoa\Websocket\Node
     */
    public function getNode()
    {
        return $this->getServer()->getConnection()->getCurrentNode();
    }

    /**
     * Returns current server
     *
     * @return \Hoa\Websocket\Server
     */
    public function getServer()
    {
        return $this->getBucket()->getSource();
    }

    /**
     * Load
     */
    public function onLoaded()
    {
    }

    /**
     * Set current bucket
     *
     * @param Bucket $bucket
     *
     * @return self
     */
    public function setBucket(Bucket $bucket = null)
    {
        $this->bucket = $bucket;

        return $this;
    }

    /**
     * Set current logger
     *
     * @param Atipik\Hoa\WebSocketBundle\Log\Logger $logger
     *
     * @return self
     */
    public function setLogger(Logger $logger = null)
    {
        $this->logger = $logger;

        return $this;
    }
}
