<?php

namespace Atipik\Hoa\WebSocketBundle\Tests\Units\Command;

use atoum;

use Atipik\Hoa\WebSocketBundle\Command\ServerCommand        as TestedClass;

use mock\Atipik\Hoa\WebSocketBundle\Log\Logger              as mockLogger;
use mock\Atipik\Hoa\WebSocketBundle\WebSocket\Runner        as mockRunner;
use mock\Symfony\Component\Console\Input\InputInterface           as mockInput;
use mock\Symfony\Component\Console\Output\OutputInterface         as mockOutput;
use mock\Symfony\Component\DependencyInjection\ContainerInterface as mockContainer;

class ServerCommand extends atoum
{
    public function testConfigure()
    {
        $this
            ->given($command = new TestedClass)
                ->string($command->getName())
                    ->isEqualTo('hoa:websocketserver')
                ->string($command->getDescription())
                    ->isEqualTo('Starts a web socket server')
                ->string($command->getHelp())
                    ->isEqualTo('hoa:websocketserver [-g group1 [-g group2]]')
                ->array($options = $command->getDefinition()->getOptions())
                    ->hasSize(1)
                    ->object($option = $options['group'])
                        ->isInstanceOf('Symfony\Component\Console\Input\InputOption')
                    ->string($option->getName())
                        ->isEqualTo('group')
                    ->string($option->getShortcut())
                        ->isEqualTo('g')
                    ->string($option->getDescription())
                        ->isEqualTo('Specify modules groups to launch. If none, all modules will be launched.')
                    ->boolean($option->isArray())
                        ->isTrue()
                    ->boolean($option->isValueRequired())
                        ->isTrue()
        ;
    }


    public function testGetRunner()
    {
        $this
            ->given($this->getMockGenerator()->orphanize('__construct'))
            ->given($mockRunner = new mockRunner)
            ->given($mockLogger = new mockLogger)
            ->given($mockRunner->setLogger($mockLogger))
            ->given($mockContainer = new mockContainer)
            ->given(
                $this->calling($mockContainer)->get = function($service) use($mockRunner, $mockLogger) {
                    switch($service) {
                        case 'hoa.websocket.logger':
                            return $mockLogger;
                        case 'hoa.websocket.runner':
                            return $mockRunner;
                    }
                }
            )
            ->given($mockInput  = new mockInput)
            ->given($mockOutput = new mockOutput)
            ->given($command = new TestedClass)
            ->given($command->setContainer($mockContainer))

            ->object($runner = $command->getRunner($mockInput, $mockOutput))
                ->isIdenticalTo($mockRunner)
            ->object($runner->getLogger()->getOutput())
                ->isIdenticalTo($mockOutput)
            ->array($runner->getGroups())
                ->isEmpty()

            ->given(
                $this->calling($mockInput)->getOption = $groups = array('foo', 'bar')
            )

            ->object($runner = $command->getRunner($mockInput, $mockOutput))
                ->isIdenticalTo($mockRunner)
            ->array($runner->getGroups())
                ->isEqualTo($groups)
        ;
    }
}