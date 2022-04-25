<?php

declare(strict_types=1);

namespace Tests\Emitter\Exception;

use PHPUnit\Framework\TestCase;
use Spiral\Sapi\Emitter\Exception\EmitterException;

final class EmitterExceptionTest extends TestCase
{
    public function testExceptionWithPreviousSet(): void
    {
        $e = new EmitterException('', 0, new \Exception());

        $this->assertInstanceOf(\Throwable::class, $e->getPrevious());
    }
}
