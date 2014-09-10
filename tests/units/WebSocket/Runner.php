<?php

namespace Atipik\Hoa\WebSocketBundle\Tests\Units\WebSocket;

use atoum;

use Atipik\Hoa\WebSocketBundle\WebSocket\Runner                       as TestedClass;
use mock\Atipik\Hoa\WebSocketBundle\WebSocket\Runner                  as mockTestedClass;

use mock\Atipik\Hoa\WebSocketBundle\Tests\Units\WebSocket\testModule1 as mockTestModule1;
use mock\Atipik\Hoa\WebSocketBundle\Tests\Units\WebSocket\testModule2 as mockTestModule2;
use mock\Atipik\Hoa\WebSocketBundle\Log\Logger                        as mockLogger;
use mock\Atipik\Hoa\WebSocketBundle\WebSocket\Server                  as mockWebSocketServer;
use mock\Hoa\Core\Event\Bucket                                              as mockBucket;
use mock\Symfony\Component\Console\Output\OutputInterface                   as mockOutput;


class Runner extends atoum
{
    public function testInitWebSocketServer()
    {
        $this
            ->given($mockWebSocketServer = $this->generateMockWebSocketServer())
            ->given($this->getMockGenerator()->orphanize('__construct'))
            ->given($this->calling($mockWebSocketServer)->on            = $mockWebSocketServer)
            ->given($this->calling($mockWebSocketServer)->getConnection = $mockConnection = new \mock\Hoa\Socket\Connection)
            ->given($this->getMockGenerator()->orphanize('__construct'))
            ->given($this->calling($mockConnection)->getSocket          = $mockSocket     = new \mock\Hoa\Socket)
            ->given($this->calling($mockSocket)->getAddress             = $address        = uniqid())
            ->given($this->calling($mockSocket)->getPort                = $port           = uniqid())
            ->given($runner = new mockTestedClass($mockWebSocketServer, $logger = new mockLogger))
            ->given($runner->addModule($module1 = new testModule1))
            ->given($runner->addModule($module2 = new testModule2))

            ->assert('Without node class')
                ->object($runner->initWebSocketServer())
                    ->isIdenticalTo($mockWebSocketServer)
                ->mock($logger)
                    ->call('log')
                        ->exactly(13)
                        ->withIdenticalArguments(
                            '<fg=yellow>Starting server...</fg=yellow>'
                        )
                            ->once()
                        ->withIdenticalArguments(
                            'Environment: <fg=green>%s</fg=green>'
                        )
                            ->once()
                        ->withIdenticalArguments(
                            'Class used:'
                        )
                            ->once()
                        ->withIdenticalArguments(
                            '  Logger           : %s',
                            get_class($logger)
                        )
                            ->once()
                        ->withIdenticalArguments(
                            '  Runner           : %s',
                            get_class($runner)
                        )
                            ->once()
                        ->withIdenticalArguments(
                            '  WebSocket Server : %s',
                            get_class($mockWebSocketServer)
                        )
                            ->once()
                        ->withIdenticalArguments(
                            '  Socket Server    : %s',
                            get_class($mockSocket)
                        )
                            ->once()
                        ->withIdenticalArguments(
                            '  Node             : %s',
                            'Hoa\Socket\Node'
                        )
                            ->once()
                        ->withIdenticalArguments(
                            '<fg=yellow>Listening on %s:%d</fg=yellow>',
                            $address,
                            $port
                        )
                            ->once()
                        ->withIdenticalArguments(
                            'Listening <fg=green>%s</fg=green> event <fg=yellow>%s</fg=yellow>::<fg=blue>%s</fg=blue>',
                            'event1',
                            'Atipik\Hoa\WebSocketBundle\Tests\Units\WebSocket\testModule1',
                            'onEvent1'
                        )
                            ->once()
                        ->withIdenticalArguments(
                            'Listening <fg=green>%s</fg=green> event <fg=yellow>%s</fg=yellow>::<fg=blue>%s</fg=blue>',
                            'event2',
                            'Atipik\Hoa\WebSocketBundle\Tests\Units\WebSocket\testModule1',
                            'onEvent2'
                        )
                            ->once()
                        ->withIdenticalArguments(
                            'Listening <fg=green>%s</fg=green> event <fg=yellow>%s</fg=yellow>::<fg=blue>%s</fg=blue>',
                            'event3',
                            'Atipik\Hoa\WebSocketBundle\Tests\Units\WebSocket\testModule1',
                            'onEvent3'
                        )
                            ->once()
                        ->withIdenticalArguments(
                            'Listening <fg=green>%s</fg=green> event <fg=yellow>%s</fg=yellow>::<fg=blue>%s</fg=blue>',
                            'event2',
                            'Atipik\Hoa\WebSocketBundle\Tests\Units\WebSocket\testModule2',
                            'onEvent2'
                        )
                            ->once()
                ->mock($mockWebSocketServer)
                    ->call('on')
                        ->exactly(6)
                        ->withArguments(
                            'open',
                            xcallable($runner, 'onOpen')
                        )
                            ->once()
                        ->withArguments(
                            'message',
                            xcallable($runner, 'onMessage')
                        )
                            ->once()
                        ->withArguments(
                            'binary-message',
                            xcallable($runner, 'onBinaryMessage')
                        )
                            ->once()
                        ->withArguments(
                            'ping',
                            xcallable($runner, 'onPing')
                        )
                            ->once()
                        ->withArguments(
                            'error',
                            xcallable($runner, 'onError')
                        )
                            ->once()
                        ->withArguments(
                            'close',
                            xcallable($runner, 'onClose')
                        )
                            ->once()
                ->mock($mockConnection)
                    ->call('setNodeName')
                        ->never()

            ->assert('With node class')
            ->given($runner->setNodeClass($class = uniqid()))
                ->object($runner->initWebSocketServer())
                    ->isIdenticalTo($mockWebSocketServer)
                ->mock($mockConnection)
                    ->call('setNodeName')
                        ->once()
                        ->withIdenticalArguments($class)
        ;
    }

    public function testAddHasEvent_SetGetEvents_GetEventCallables()
    {
        $this
            ->given($runner = new TestedClass($this->generateMockWebSocketServer(), new mockLogger))

            ->exception(
                function() use($runner, &$callable) {
                    $runner->addEvent(uniqid(), $callable = uniqid());
                }
            )
                ->isInstanceOf('InvalidArgumentException')
                ->hasMessage('Atipik\Hoa\WebSocketBundle\WebSocket\Runner::addEvent expects parameter 2 to be callable, string given: ' . $callable)

            ->if($runner->addEvent($event = uniqid(), $callable = array(__CLASS__, __FUNCTION__)))
                ->boolean($runner->hasEvent($event))
                    ->isTrue()
                ->boolean($runner->hasEvent($event . uniqid()))
                    ->isFalse()
                ->array($runner->getEvents())
                    ->isEqualTo(
                        array(
                            $event => array(
                                $callable
                            )
                        )
                    )
                ->array($runner->getEventCallables($event))
                    ->isEqualTo(array($callable))
                ->array($runner->getEventCallables($event . uniqid()))
                    ->isEmpty()

            ->object($runner->setEvents($events = array(uniqid())))
                ->isIdenticalTo($runner)
            ->array($runner->getEvents())
                ->isIdenticalTo($events)
        ;
    }

    public function testConstruct()
    {
        $this
            ->given($mockWebSocketServer = $this->generateMockWebSocketServer())

            ->if($runner = new TestedClass($mockWebSocketServer, $logger = new mockLogger))
                ->array($runner->getEvents())
                    ->isEmpty()
                ->variable($runner->getNodeClass())
                    ->isNull()
                ->variable($runner->getLogger())
                    ->isIdenticalTo($logger)
                ->object($runner->getWebSocketServer())
                    ->isIdenticalTo($mockWebSocketServer)

            ->if($runner = new TestedClass($mockWebSocketServer, $logger = new mockLogger, $nodeClass = uniqid()))
                ->array($runner->getEvents())
                    ->isEmpty()
                ->string($runner->getNodeClass())
                    ->isEqualTo($nodeClass)
                ->variable($runner->getLogger())
                    ->isIdenticalTo($logger)
                ->object($runner->getWebSocketServer())
                    ->isIdenticalTo($mockWebSocketServer)
        ;
    }

    public function testOnBinaryMessage()
    {
        $this
            ->given($runner = new mockTestedClass($this->generateMockWebSocketServer(), new mockLogger))
            ->given($this->calling($runner)->onEvent = null)

            ->if($runner->onBinaryMessage($bucket = new mockBucket))
                ->mock($runner)
                    ->call('onEvent')
                        ->once()
                        ->withIdenticalArguments('binary-message', $bucket)
                            ->once()
        ;
    }

    public function testOnClose()
    {
        $this
            ->given($runner = new mockTestedClass($this->generateMockWebSocketServer(), new mockLogger))
            ->given($this->calling($runner)->onEvent = null)

            ->if($runner->onClose($bucket = new mockBucket))
                ->mock($runner)
                    ->call('onEvent')
                        ->once()
                        ->withIdenticalArguments('close', $bucket)
                            ->once()
        ;
    }

    public function testOnError()
    {
        $this
            ->given($runner = new mockTestedClass($this->generateMockWebSocketServer(), new mockLogger))
            ->given($this->calling($runner)->onEvent = null)

            ->if($runner->onError($bucket = new mockBucket))
                ->mock($runner)
                    ->call('onEvent')
                        ->once()
                        ->withIdenticalArguments('error', $bucket)
                            ->once()
        ;
    }

    public function testLoadEvents()
    {
        $this
            ->given($logger = new mockLogger)
            ->given($logger->setOutput(new mockOutput))
            ->given($runner = new mockTestedClass($this->generateMockWebSocketServer(), new mockLogger))
            ->given($runner->addModule($module1 = new mocktestModule1))
            ->given($runner->addModule($module2 = new mocktestModule2))

            ->if($runner->setGroups(array()))
            ->and($runner->loadEvents())
                ->array($runner->getEvents())
                    ->isEqualTo(
                        array(
                            'event1' => array(
                                array($module1, 'onEvent1'),
                            ),
                            'event2' => array(
                                array($module1, 'onEvent2'),
                                array($module2, 'onEvent2'),
                            ),
                            'event3' => array(
                                array($module1, 'onEvent3'),
                            ),
                        )
                    )

            ->if($runner->setGroups(array('foo')))
            ->and($runner->loadEvents())
                ->array($runner->getEvents())
                    ->isEqualTo(
                        array(
                            'event1' => array(
                                array($module1, 'onEvent1'),
                            ),
                            'event2' => array(
                                array($module1, 'onEvent2'),
                            ),
                            'event3' => array(
                                array($module1, 'onEvent3'),
                            ),
                        )
                    )

            ->if($runner->setGroups(array('bar')))
            ->and($runner->loadEvents())
                ->array($runner->getEvents())
                    ->isEqualTo(
                        array(
                            'event2' => array(
                                array($module2, 'onEvent2'),
                            ),
                        )
                    )

            ->if($runner->setGroups(array('foo', 'bar')))
            ->and($runner->loadEvents())
                ->array($runner->getEvents())
                    ->isEqualTo(
                        array(
                            'event1' => array(
                                array($module1, 'onEvent1'),
                            ),
                            'event2' => array(
                                array($module1, 'onEvent2'),
                                array($module2, 'onEvent2'),
                            ),
                            'event3' => array(
                                array($module1, 'onEvent3'),
                            ),
                        )
                    )
        ;
    }

    public function testOnEvent()
    {
        $this
            ->given($logger = new mockLogger)
            ->given($logger->setOutput(new mockOutput))
            ->given($runner = new mockTestedClass($this->generateMockWebSocketServer(), $logger))
            ->given($runner->addModule($module1 = new mocktestModule1))
            ->given($runner->addModule($module2 = new mocktestModule2))
            ->given($runner->loadEvents())

            ->given($mockBucket = new mockBucket)
            ->given($this->calling($mockBucket)->getData            = $rawData        = uniqid())
            ->given($this->calling($mockBucket)->getSource          = $mockSource     = new mockWebSocketServer)
            ->given($this->getMockGenerator()->orphanize('__construct'))
            ->given($this->calling($mockSource)->getConnection      = $mockConnection = new \mock\Hoa\Socket\Connection)
            ->given($this->getMockGenerator()->orphanize('__construct'))
            ->given($this->calling($mockConnection)->getCurrentNode = $mockNode       = new \mock\Hoa\Socket\Node)
            ->given($this->calling($mockNode)->getId                = $nodeId         = uniqid())

            ->assert('No log')
                ->if($runner->onEvent(uniqid(), $mockBucket, null, false))
                    ->mock($logger)
                        ->call('log')
                            ->never()

            ->assert('Log')
                ->if($runner->onEvent($event = uniqid(), $mockBucket))
                    ->mock($logger)
                        ->call('log')
                            ->once()
                            ->withIdenticalArguments(
                                '%s - %s (%d) > %s + %s',
                                $nodeId,
                                $event,
                                0,
                                json_encode($rawData),
                                json_encode(null)
                            )
                                ->once()

            ->assert('Log with array raw data')
                ->given(
                    $this->calling($mockBucket)->getData = $rawData = array(
                        uniqid() => uniqid(),
                        uniqid() => uniqid(),
                    )
                )
                ->if($runner->onEvent($event = uniqid(), $mockBucket))
                    ->mock($logger)
                        ->call('log')
                            ->once()
                            ->withIdenticalArguments(
                                '%s - %s (%d) > %s + %s',
                                $nodeId,
                                $event,
                                0,
                                json_encode($rawData, JSON_PRETTY_PRINT),
                                json_encode(null, JSON_PRETTY_PRINT)
                            )
                                ->once()

            ->assert('Single event')
                ->if($runner->onEvent('event1', $mockBucket))
                    ->mock($module1)
                        ->call('setBucket')
                            ->once()
                            ->withIdenticalArguments($mockBucket)
                                ->once()
                        ->call('onEvent1')
                            ->once()
                            ->withIdenticalArguments(null)
                                ->once()

            ->assert('Double event')
                ->if($runner->onEvent('event2', $mockBucket))
                    ->mock($module1)
                        ->call('setBucket')
                            ->once()
                            ->withIdenticalArguments($mockBucket)
                                ->once()
                        ->call('onEvent2')
                            ->once()
                            ->withIdenticalArguments(null)
                                ->once()
                    ->mock($module2)
                        ->call('setBucket')
                            ->once()
                            ->withIdenticalArguments($mockBucket)
                                ->once()
                        ->call('onEvent2')
                            ->once()
                            ->withIdenticalArguments(null)
                                ->once()

            ->assert('Additionnal data')
                ->if($runner->onEvent('event1', $mockBucket, $additionnalData = uniqid()))
                    ->mock($module1)
                        ->call('setBucket')
                            ->once()
                            ->withIdenticalArguments($mockBucket)
                                ->once()
                        ->call('onEvent1')
                            ->once()
                            ->withIdenticalArguments($additionnalData)
                                ->once()
        ;
    }

    public function testOnMessage()
    {
        $this
            ->given($runner = new mockTestedClass($this->generateMockWebSocketServer(), new mockLogger))
            ->given($this->calling($runner)->onEvent = null)

            ->if($runner->onMessage($bucket = new mockBucket))
                ->mock($runner)
                    ->call('onEvent')
                        ->once()
                        ->withIdenticalArguments('message', $bucket)
                            ->once()
        ;
    }

    public function testOnOpen()
    {
        $this
            ->given($runner = new mockTestedClass($this->generateMockWebSocketServer(), new mockLogger))
            ->given($this->calling($runner)->onEvent = null)

            ->if($runner->onOpen($bucket = new mockBucket))
                ->mock($runner)
                    ->call('onEvent')
                        ->once()
                        ->withIdenticalArguments('open', $bucket)
                            ->once()
        ;
    }

    public function testOnPing()
    {
        $this
            ->given($runner = new mockTestedClass($this->generateMockWebSocketServer(), new mockLogger))
            ->given($this->calling($runner)->onEvent = null)

            ->if($runner->onPing($bucket = new mockBucket))
                ->mock($runner)
                    ->call('onEvent')
                        ->once()
                        ->withIdenticalArguments('ping', $bucket)
                            ->once()
        ;
    }

    public function testSetGetNodeClass()
    {
        $this
            ->given($runner = new TestedClass($this->generateMockWebSocketServer(), new mockLogger))

            ->object($runner->setNodeClass($class = uniqid()))
                ->isIdenticalTo($runner)
            ->string($runner->getNodeClass())
                ->isIdenticalTo($class)
        ;
    }

    public function testSetGetLogger()
    {
        $this
            ->given($runner = new TestedClass($this->generateMockWebSocketServer(), new mockLogger))

            ->object($runner->setLogger($logger = new mockLogger))
                ->isIdenticalTo($runner)
            ->object($runner->getLogger())
                ->isIdenticalTo($logger)
        ;
    }

    public function testSetGetGroups()
    {
        $this
            ->given($runner = new TestedClass($this->generateMockWebSocketServer(), new mockLogger))

            ->object($runner->setGroups($groups = array(uniqid(), uniqid())))
                ->isIdenticalTo($runner)
            ->array($runner->getGroups())
                ->isIdenticalTo($groups)
        ;
    }

    public function testSetGetModules_AddModule()
    {
        $this
            ->given($runner = new TestedClass($this->generateMockWebSocketServer(), new mockLogger))

            ->object($runner->setModules(array($module1 = new testModule1)))
                ->isIdenticalTo($runner)
            ->array($runner->getModules())
                ->isIdenticalTo(array($module1))

            ->object($runner->addModule($module2 = new testModule2))
                ->isIdenticalTo($runner)
            ->array($runner->getModules())
                ->isIdenticalTo(array($module1, $module2))
        ;
    }

    public function testSetGetWebSocketServer()
    {
        $this
            ->given($runner = new TestedClass($this->generateMockWebSocketServer(), new mockLogger))

            ->object($runner->setWebSocketServer($mockWebSocketServer = $this->generateMockWebSocketServer()))
                ->isIdenticalTo($runner)
            ->object($runner->getWebSocketServer())
                ->isIdenticalTo($mockWebSocketServer)
        ;
    }

    private function generateMockWebSocketServer()
    {
        $this->getMockGenerator()->orphanize('__construct');

        return new mockWebSocketServer;
    }
}



use Atipik\Hoa\WebSocketBundle\WebSocket\Module\Module;

class testModule1 extends Module
{
    public function getSubscribedEvents()
    {
        return array(
            'event1' => 'onEvent1',
            'event2' => 'onEvent2',
            'event3' => 'onEvent3',
        );
    }

    public function getGroup()
    {
        return 'foo';
    }

    public function onEvent1()
    {
    }

    public function onEvent2()
    {
    }

    public function onEvent3()
    {
    }
}

class testModule2 extends Module
{
    public function getSubscribedEvents()
    {
        return array(
            'event2' => 'onEvent2',
        );
    }

    public function getGroup()
    {
        return 'bar';
    }

    public function onEvent2()
    {
    }
}
