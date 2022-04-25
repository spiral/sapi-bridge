<?php

declare(strict_types=1);

namespace Tests\Bootloader;

use Spiral\Sapi\Dispatcher\SapiDispatcher;
use Tests\TestCase;

final class SapiBootloaderTest extends TestCase
{
    public function testDispatcherShouldBeRegistered()
    {
        $this->assertDispatcherRegistered(SapiDispatcher::class);
    }
}
