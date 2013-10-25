<?php

namespace Atipik\Hoa\WebSocketBundle\Tests\Units\Log;

use atoum;

use Atipik\Hoa\WebSocketBundle\Log\Logger           as TestedClass;

use mock\Symfony\Component\Console\Output\OutputInterface as mockOutput;

class Logger extends atoum
{
    public function testConstruct()
    {
        $this
            ->given($logger = new TestedClass)
                ->variable($logger->getOutput())
                    ->isNull()

            ->given($logger = new TestedClass($output = new mockOutput))
                ->object($logger->getOutput())
                    ->isIdenticalTo($output)
        ;
    }

    public function testError()
    {
        $this
            ->given($logger = new TestedClass($output = new mockOutput))

            ->if(
                $logger->error(
                    $format = uniqid() . '%s %s' . uniqid(),
                    $arg1   = uniqid(),
                    $arg2   = uniqid()
                )
            )
                ->mock($output)
                    ->call('writeln')
                        ->once()
                        ->withIdenticalArguments(
                            '<bg=red;fg=white;options=bold>' . $format . '</bg=red;fg=white;options=bold>',
                            $arg1,
                            $arg2
                        )
        ;
    }

    public function testInit()
    {
        $this
            ->given($logger = new TestedClass($output = new mockOutput))

            ->object($logger->init())
                ->isIdenticalTo($logger)
            ->variable($logger->getOutput())
                ->isNull()
        ;
    }

    public function testLog()
    {
        $this
            ->given($logger = new TestedClass($output = new mockOutput))
            ->given($this->function->date = $date = date('d/m/Y H:i:s'))

            ->assert('only format')
                ->if($logger->log($format = uniqid()))
                    ->mock($output)
                        ->call('writeln')
                            ->once()
                            ->withIdenticalArguments(
                                sprintf(
                                    '<fg=green>[%s]</fg=green> ' . $format,
                                    $date
                                )
                            )
                                ->once()

            ->assert('format + arg')
                ->if($logger->log($format = uniqid() . '/ %s', $arg = uniqid()))
                    ->mock($output)
                        ->call('writeln')
                            ->once()
                            ->withIdenticalArguments(
                                sprintf(
                                    '<fg=green>[%s]</fg=green> ' . $format,
                                    $date,
                                    $arg
                                )
                            )
                                ->once()

            ->assert('format + args')
                ->if($logger->log($format = uniqid() . '/ %s / %s', $arg1 = uniqid(), $arg2 = uniqid()))
                    ->mock($output)
                        ->call('writeln')
                            ->once()
                            ->withIdenticalArguments(
                                sprintf(
                                    '<fg=green>[%s]</fg=green> ' . $format,
                                    $date,
                                    $arg1,
                                    $arg2
                                )
                            )
                                ->once()

            ->assert('prefix + only format')
                ->if($logger->setPrefix($prefix = uniqid()))
                ->and($logger->log($format = uniqid()))
                    ->mock($output)
                        ->call('writeln')
                            ->once()
                            ->withIdenticalArguments(
                                sprintf(
                                    '<fg=green>[%s]</fg=green> <fg=blue>%s</fg=blue> > ' . $format,
                                    $date,
                                    $prefix
                                )
                            )
                                ->once()

            ->assert('next time, there is no prefix')
                ->if($logger->log($format))
                    ->mock($output)
                        ->call('writeln')
                            ->once()
                            ->withIdenticalArguments(
                                sprintf(
                                    '<fg=green>[%s]</fg=green> ' . $format,
                                    $date
                                )
                            )
                                ->once()

            ->assert('prefix + format + arg')
                ->if($logger->setPrefix($prefix = uniqid()))
                ->and($logger->log($format = uniqid() . '/ %s', $arg = uniqid()))
                    ->mock($output)
                        ->call('writeln')
                            ->once()
                            ->withIdenticalArguments(
                                sprintf(
                                    '<fg=green>[%s]</fg=green> <fg=blue>%s</fg=blue> > ' . $format,
                                    $date,
                                    $prefix,
                                    $arg
                                )
                            )
                                ->once()

            ->assert('prefix + format + args')
                ->if($logger->setPrefix($prefix = uniqid()))
                ->and($logger->log($format = uniqid() . '/ %s / %s', $arg1 = uniqid(), $arg2 = uniqid()))
                    ->mock($output)
                        ->call('writeln')
                            ->once()
                            ->withIdenticalArguments(
                                sprintf(
                                    '<fg=green>[%s]</fg=green> <fg=blue>%s</fg=blue> > ' . $format,
                                    $date,
                                    $prefix,
                                    $arg1,
                                    $arg2
                                )
                            )
                                ->once()
        ;
    }

    public function testSetGetOutput()
    {
        $this
            ->given($logger = new TestedClass)

            ->object($logger->setOutput($output = new mockOutput))
                ->isIdenticalTo($logger)
            ->object($logger->getOutput())
                ->isIdenticalTo($output)
        ;
    }

    public function testSetGetPrefix()
    {
        $this
            ->given($logger = new TestedClass)

            ->object($logger->setPrefix($prefix = uniqid()))
                ->isIdenticalTo($logger)
            ->string($logger->getPrefix())
                ->isIdenticalTo($prefix)
        ;
    }

    public function testSuccess()
    {
        $this
            ->given($logger = new TestedClass($output = new mockOutput))

            ->if(
                $logger->success(
                    $format = uniqid() . '%s %s' . uniqid(),
                    $arg1   = uniqid(),
                    $arg2   = uniqid()
                )
            )
                ->mock($output)
                    ->call('writeln')
                        ->once()
                        ->withIdenticalArguments(
                            '<fg=green>' . $format . '</fg=green>',
                            $arg1,
                            $arg2
                        )
        ;
    }

    public function testWarning()
    {
        $this
            ->given($logger = new TestedClass($output = new mockOutput))

            ->if(
                $logger->warning(
                    $format = uniqid() . '%s %s' . uniqid(),
                    $arg1   = uniqid(),
                    $arg2   = uniqid()
                )
            )
                ->mock($output)
                    ->call('writeln')
                        ->once()
                        ->withIdenticalArguments(
                            '<fg=yellow>' . $format . '</fg=yellow>',
                            $arg1,
                            $arg2
                        )
        ;
    }
}