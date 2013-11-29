<?php

namespace Atipik\Hoa\WebSocketBundle\WebSocket\Exception;

use Exception;

/**
 * Quit
 */
class Quit extends Exception
{
    /**
     * Constructor
     *
     * @param string    $message
     * @param integer   $code
     * @param Exception $previous
     */
    public function __construct($message = 'Server stopped.', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}