<?php

namespace Atipik\Hoa\WebSocketBundle\WebSocket\Module;

use Atipik\Hoa\WebSocketBundle\Log\Logger;
use Hoa\Core\Event\Bucket;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Module interface
 */
interface ModuleInterface
{
    /**
     * Returns current bucket
     *
     * @return Hoa\Core\Event\Bucket
     */
    public function getBucket();

    /**
     * Returns group name
     *
     * @return string
     */
    public function getGroup();

    /**
     * Returns current logger
     *
     * @return Atipik\Hoa\WebSocketBundle\Log\Logger
     */
    public function getLogger();

    /**
     * Returns current node
     *
     * @return \Hoa\Websocket\Node
     */
    public function getNode();

    /**
     * Returns current server
     *
     * @return \Hoa\Websocket\Node
     */
    public function getServer();

    /**
     * Returns subscribed events
     *
     * @return array
     */
    public function getSubscribedEvents();

    /**
     * Load
     */
    public function onLoaded();

    /**
     * Set current bucket
     *
     * @param Hoa\Core\Event\Bucket $bucket
     *
     * @return self
     */
    public function setBucket(Bucket $bucket = null);

    /**
     * Set current logger
     *
     * @param Atipik\Hoa\WebSocketBundle\Log\Logger $logger
     *
     * @return self
     */
    public function setLogger(Logger $logger = null);
}
