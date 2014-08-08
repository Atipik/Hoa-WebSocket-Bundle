<?php

namespace Atipik\Hoa\WebSocketBundle\Log;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Logger
 *
 * hoa.websocket.logger service
 */
class Logger
{
    protected $output;
    protected $prefix;

    /**
     * Constructor
     *
     * @param Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(OutputInterface $output = null)
    {
        $this
            ->init()
            ->setOutput($output)
        ;
    }

    /**
     * Log error messages
     *
     * @param string $format
     */
    public function error($format/*, $arg1, $arg2, ... $argN */)
    {
        $args = func_get_args();
        $args[0] = '<bg=red;fg=white;options=bold>' . $format . '</bg=red;fg=white;options=bold>';

        call_user_func_array(array($this, 'log'), $args);
    }

    /**
     * Returns current output object
     *
     * @return Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Returns prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Initialization
     *
     * @return self
     */
    public function init()
    {
        return $this
            ->setOutput(null)
            ->setPrefix('')
        ;
    }

    /**
     * Log messages
     *
     * @param string $format
     */
    public function log($format/*, $arg1, $arg2, ... $argn */)
    {
        $output = $this->getOutput();

        if ($output) {
            $args = func_get_args();
            $args = array_slice($args, 1);

            // add date
            $date = sprintf(
                '<fg=green>[%s]</fg=green> ',
                date('d/m/Y H:i:s')
            );

            // add prefix
            if ($prefix = $this->getPrefix()) {
                $prefix = sprintf(
                    '<fg=blue>%s</fg=blue> > ',
                    $prefix
                );
            }

            $output->writeln(
                vsprintf(
                    $date . $prefix . $format,
                    $args
                )
            );
        }

        $this->setPrefix('');
    }

    /**
     * Set current output object
     *
     * @param Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return self
     */
    public function setOutput(OutputInterface $output = null)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Set prefix
     *
     * @param string $prefix
     *
     * @return self
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Log success messages
     *
     * @param string $format
     */
    public function success($format/*, $arg1, $arg2, ... $argn */)
    {
        $args = func_get_args();
        $args[0] = '<fg=green>' . $format . '</fg=green>';

        call_user_func_array(array($this, 'log'), $args);
    }

    /**
     * Log warning messages
     *
     * @param string $format
     */
    public function warning($format/*, $arg1, $arg2, ... $argn */)
    {
        $args = func_get_args();
        $args[0] = '<fg=yellow>' . $format . '</fg=yellow>';

        call_user_func_array(array($this, 'log'), $args);
    }
}