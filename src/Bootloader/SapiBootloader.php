<?php

declare(strict_types=1);

namespace Spiral\Sapi\Bootloader;

use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Spiral\Boot\AbstractKernel;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Http\Config\HttpConfig;
use Spiral\Sapi\Dispatcher\SapiDispatcher;
use Spiral\Sapi\Emitter\SapiEmitter;

final class SapiBootloader extends Bootloader implements SingletonInterface
{
    protected const SINGLETONS = [
        SapiEmitter::class => [self::class, 'createEmitter'],
        ServerRequestCreatorInterface::class => ServerRequestCreator::class,
    ];

    public function createEmitter(HttpConfig $config): SapiEmitter
    {
        $emitter = new SapiEmitter();
        if (($chunkSize = $config->getChunkSize()) !== null) {
            $emitter->bufferSize = $chunkSize;
        }
        return $emitter;
    }

    public function init(AbstractKernel $kernel): void
    {
        // Lowest priority
        $kernel->booted(static function (AbstractKernel $kernel): void {
            $kernel->addDispatcher(SapiDispatcher::class);
        });
    }
}
