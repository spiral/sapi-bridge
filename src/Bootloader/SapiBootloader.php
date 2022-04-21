<?php

declare(strict_types=1);

namespace Spiral\Sapi\Bootloader;

use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Spiral\Boot\AbstractKernel;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Bootloader\Http\DiactorosBootloader;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Core\FactoryInterface;
use Spiral\Http\Config\HttpConfig;
use Spiral\Sapi\Dispatcher\SapiDispatcher;
use Spiral\Sapi\Emitter\SapiEmitter;

final class SapiBootloader extends Bootloader implements SingletonInterface
{
    protected const DEPENDENCIES = [
        DiactorosBootloader::class,
    ];

    protected const SINGLETONS = [
        SapiEmitter::class => [self::class, 'createEmitter'],
        ServerRequestCreatorInterface::class => ServerRequestCreator::class,
    ];

    public function createEmitter(HttpConfig $config): SapiEmitter
    {
        return new SapiEmitter();

        /* TODO wait v2.13
        $emitter = new SapiEmitter();
        if (($chunkSize = $config->getChunkSize()) !== null) {
            $emitter->bufferSize = $chunkSize;
        }
        */
    }

    public function boot(AbstractKernel $kernel, FactoryInterface $factory): void
    {
        // Lowest priority
        $kernel->started(static function (AbstractKernel $kernel) use ($factory): void {
            $kernel->addDispatcher($factory->make(SapiDispatcher::class));
        });
    }
}
