<?php

namespace Atipik\Hoa\WebSocketBundle\Command;

use Atipik\Hoa\WebSocketBundle\WebSocket\Runner;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Launch server
 */
class ServerCommand extends ContainerAwareCommand
{
    /**
     * Returns server
     *
     * @param Symfony\Component\Console\Input\InputInterface   $input
     * @param Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return Atipik\Hoa\WebSocketBundle\WebSocket\Runner
     */
    public function getRunner(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        // inject output in logger
        $container->get('hoa.websocket.logger')->setOutput($output);

        // get runner
        $runner = $container->get('hoa.websocket.runner');

        $groups = $input->getOption('group');

        if (!empty($groups)) {
            $runner->setGroups($groups);
        }

        return $runner;
    }

    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('hoa:websocketserver')
            ->setDescription('Starts a web socket server')
            ->setHelp('hoa:websocketserver [-g group1 [-g group2]]')
            ->addOption(
                'group', 'g',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Specify modules groups to launch. If none, all modules will be launched.'
            )
        ;
    }

    /**
     * Execute command
     *
     * @param Symfony\Component\Console\Input\InputInterface   $input
     * @param Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->getRunner($input, $output)->run() ? 0 : -1;
    }
}
