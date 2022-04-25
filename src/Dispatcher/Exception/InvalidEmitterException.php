<?php

declare(strict_types=1);

namespace Spiral\Sapi\Dispatcher\Exception;

final class InvalidEmitterException extends \RuntimeException
{
    public function __construct(string $message = 'The Emitter class must have a public `emit` method.')
    {
        parent::__construct($message);
    }
}
