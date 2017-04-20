<?php

namespace TMPHP\RestApiGenerators\Exceptions;

use Throwable;

/**
 * Custom exception about wrong type of data
 *
 * Class WrongTypeException
 *
 * @package App\Exceptions
 */
class WrongTypeException extends \Exception
{
    /**
     * Exception of wrong type
     *
     * WrongTypeException constructor
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}