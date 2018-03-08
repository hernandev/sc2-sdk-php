<?php

namespace SteemConnect\Exceptions;

use Throwable;

/**
 * Class ConfigException.
 *
 * Configuration related exceptions.
 */
class ConfigException extends ClientException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}