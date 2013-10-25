<?php

namespace Atipik\Hoa\WebSocketBundle\WebSocket;

use Atipik\Hoa\WebSocketBundle\Log\Logger;
use Atipik\Hoa\WebSocketBundle\WebSocket\Server as WebSocketServer;
use Hoa\Core\Event\Bucket;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Bundle runner
 *
 * Called by command
 * hoa.websocket.runner service
 */
class Runner
{
    const DEFAULT_ADDRESS    = '127.0.0.1';
    const DEFAULT_PORT       = '8080';

    protected $events  = array();
    protected $groups  = array();
    protected $logger;
    protected $modules = array();
    protected $nodeClass;
    protected $webSocketServer;

    /**
     * Constructor
     *
     * @param Hoa\Websocket\Server                  $webSocketServer
     * @param Atipik\Hoa\WebSocketBundle\Log\Logger $logger
     * @param string                                $nodeClass
     */
    public function __construct(WebSocketServer $webSocketServer, Logger $logger, $nodeClass = null)
    {
        $this
            ->init()
            ->setLogger($logger)
            ->setNodeClass($nodeClass)
            ->setWebSocketServer($webSocketServer)
        ;
    }

    /**
     * Add an event and its callback
     *
     * @param string   $event
     * @param callable $callable
     */
    public function addEvent($event, $callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s expects parameter 2 to be callable, %s given: %s',
                    __METHOD__,
                    gettype($callable),
                    print_r($callable, true)
                )
            );
        }

        if (!isset($this->events[$event])) {
            $this->events[$event] = array();
        }

        $this->events[$event][] = $callable;
    }

    /**
     * Link module to this server
     *
     * Called by Atipik\Hoa\WebSocketBundle\DependencyInjection\Compiler\ModulesCompilerPass
     *
     * @param Atipik\Hoa\WebSocketBundle\WebSocket\Module\ModuleInterface $module
     *
     * @return self
     */
    public function addModule(Module\ModuleInterface $module)
    {
        $this->modules[] = $module;

        return $this;
    }

    /**
     * Returns callables event
     *
     * @param string $event
     *
     * @return array
     */
    public function getEventCallables($event)
    {
        return $this->hasEvent($event) ? $this->events[$event] : array();
    }

    /**
     * Returns all events
     *
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Returns groups
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

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
     * Returns modules
     *
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Returns current node class
     *
     * @return string
     */
    public function getNodeClass()
    {
        return $this->nodeClass;
    }

    /**
     * Returns WebSocket server
     *
     * @return Hoa\Websocket\Server
     */
    public function getWebSocketServer()
    {
        return $this->webSocketServer;
    }

    /**
     * Checks if an event with the name $event is registered
     *
     * @param string $event
     *
     * @return boolean
     */
    public function hasEvent($event)
    {
        return array_key_exists($event, $this->events);
    }

    /**
     * Initialization
     *
     * @return self
     */
    public function init()
    {
        return $this
            ->setEvents(array())
            ->setGroups(array())
            ->setModules(array())
            ->setNodeClass(null)
            ->setLogger(null)
            ->setWebSocketServer(null)
        ;
    }

    /**
     * Initialize WebSocket server
     *
     * @return Hoa\Websocket\Server
     */
    public function initWebSocketServer()
    {
        $webSocketServer = $this->getWebSocketServer();

        $webSocketServer->setLogger(
            $this->getLogger()
        );

        if ($this->getNodeClass() !== null) {
            $webSocketServer->getConnection()->setNodeName(
                $this->getNodeClass()
            );
        }

        $socket = $webSocketServer->getConnection()->getSocket();

        $this->getLogger()->log('<fg=yellow>Starting server...</fg=yellow>');
        $this->getLogger()->log('Class used:');
        $this->getLogger()->log('  Logger           : %s', get_class($this->getLogger()));
        $this->getLogger()->log('  Runner           : %s', get_class($this));
        $this->getLogger()->log('  WebSocket Server : %s', get_class($webSocketServer));
        $this->getLogger()->log('  Socket Server    : %s', get_class($socket));
        $this->getLogger()->log('  Node             : %s', ltrim($webSocketServer->getConnection()->getNodeName(), '\\'));

        $this->getLogger()->log(
            '<fg=yellow>Listening on %s:%d</fg=yellow>',
            $socket->getAddress(),
            $socket->getPort()
        );

        $webSocketServer->on('open', xcallable($this, 'onOpen'));
        $webSocketServer->on('message', xcallable($this, 'onMessage'));
        $webSocketServer->on('binary-message', xcallable($this, 'onBinaryMessage'));
        $webSocketServer->on('ping', xcallable($this, 'onPing'));
        $webSocketServer->on('error', xcallable($this, 'onError'));
        $webSocketServer->on('close', xcallable($this, 'onClose'));

        $this->loadEvents();

        return $webSocketServer;
    }

    /**
     * Launch module action
     *
     * @param string                 $event
     * @param Module\ModuleInterface $module
     * @param string                 $method
     * @param Bucket                 $bucket
     * @param mixed                  $additionnalData
     *
     * @return boolean
     */
    public function launchModuleAction($event, Module\ModuleInterface $module, $method, Bucket $bucket, $additionnalData = null)
    {
        $module->setBucket($bucket);

        return call_user_func(
            array(
                $module,
                $method
            ),
            $additionnalData
        );
    }

    /**
     * Load events
     */
    public function loadEvents()
    {
        $this->setEvents(array());

        $groups  = $this->getGroups();
        $modules = $this->getModules();

        if (!empty($groups)) {
            foreach ($modules as $key => $module) {
                if (!in_array($module->getGroup(), $groups)) {
                    unset($modules[$key]);
                }
            }
        }

        foreach ($modules as $module) {
            foreach ($module->getSubscribedEvents() as $eventName => $method) {
                $this->addEvent(
                    $eventName,
                    array(
                        $module,
                        $method
                    )
                );

                $log = array(
                    'message' => 'Listening <fg=green>%s</fg=green> event <fg=yellow>%s</fg=yellow>::<fg=blue>%s</fg=blue>',
                                 $eventName,
                                 get_class($module),
                                 $method
                );

                if (!empty($groups)) {
                    $log['message'] .= ' <fg=cyan>[%s group]</fg=cyan>';
                    $log[]           = $module->getGroup();
                }

                call_user_func_array(
                    array($this->getLogger(), 'log'),
                    $log
                );
            }

            $module->onLoaded();
        }
    }

    /**
     * Fire a "binary message" event
     *
     * @param Hoa\Core\Event\Bucket $bucket
     */
    public function onBinaryMessage(Bucket $bucket)
    {
        $this->onEvent('binary-message', $bucket);
    }

    /**
     * Fire a "close" event
     *
     * @param Hoa\Core\Event\Bucket $bucket
     */
    public function onClose(Bucket $bucket)
    {
        $this->onEvent('close', $bucket);
    }

    /**
     * Fire a "error" event
     *
     * @param Hoa\Core\Event\Bucket $bucket
     */
    public function onError(Bucket $bucket)
    {
        $this->onEvent('error', $bucket);
    }

    /**
     * Fire an event
     *
     * @param string                $event
     * @param Hoa\Core\Event\Bucket $bucket
     * @param mixed                 $additionnalData
     * @param boolean               $log
     *
     * @return void
     */
    public function onEvent($event, Bucket $bucket, $additionnalData = null, $log = true)
    {
        $callables = $this->getEventCallables($event);

        if ($log) {
            $rawData = $bucket->getData();

            $this->getLogger()->log(
                '%s - %s (%d) > %s + %s',
                $bucket->getSource()->getConnection()->getCurrentNode()->getId(),
                $event,
                count($callables),
                json_encode($rawData, JSON_PRETTY_PRINT),
                json_encode($additionnalData, JSON_PRETTY_PRINT)
            );
        }

        if ($this->hasEvent($event)) {
            foreach ($callables as $callable) {
                list($module, $method) = $callable;

                if ($this->launchModuleAction($event, $module, $method, $bucket, $additionnalData) === false) {
                    break;
                }
            }
        }
    }

    /**
     * Fire a "message" event
     *
     * @param Hoa\Core\Event\Bucket $bucket
     */
    public function onMessage(Bucket $bucket)
    {
        $this->onEvent('message', $bucket);
    }

    /**
     * Fire a "open" event
     *
     * @param Hoa\Core\Event\Bucket $bucket
     */
    public function onOpen(Bucket $bucket)
    {
        $this->onEvent('open', $bucket);
    }

    /**
     * Fire a "ping" event
     *
     * @param Hoa\Core\Event\Bucket $bucket
     */
    public function onPing(Bucket $bucket)
    {
        $this->onEvent('ping', $bucket);
    }

    /**
     * Run this server
     *
     * @return boolean
     */
    public function run()
    {
        try {
            $this->initWebSocketServer()->run();

            return true;
        } catch (\Exception $e) {
            $traces = array_reverse(explode(PHP_EOL, $e->getTraceAsString()));

            foreach ($traces as $key => $trace) {
                $trace = explode(' ', $trace, 2);

                $traces[$key] = sprintf(
                    '#%s > %s',
                    str_pad($key + 1, strlen(count($traces)), '0', STR_PAD_LEFT),
                    $trace[1]
                );
            }

            $this->getLogger()->error(
                "%s: %s\n%s",
                get_class($e),
                $e->getMessage(),
                implode("\n", $traces)
            );

            return false;
        }
    }

    /**
     * Set events array
     *
     * @param array $events
     *
     * @return self
     */
    public function setEvents(array $events)
    {
        $this->events = $events;

        return $this;
    }

    /**
     * Set groups
     *
     * @param array $groups
     *
     * @return self
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;

        return $this;
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

    /**
     * Set modules
     *
     * @param array $modules
     *
     * @return self
     */
    public function setModules(array $modules)
    {
        $this->modules = array();

        foreach ($modules as $module) {
            $this->addModule($module);
        }

        return $this;
    }

    /**
     * Set node class
     *
     * @param string $class
     *
     * @return self
     */
    public function setNodeClass($class)
    {
        $this->nodeClass = $class;

        return $this;
    }

    /**
     * Set WebSocket server
     *
     * @param Hoa\Websocket\Server $webSocketServer
     *
     * @return self
     */
    public function setWebSocketServer(WebSocketServer $webSocketServer = null)
    {
        $this->webSocketServer = $webSocketServer;

        return $this;
    }
}