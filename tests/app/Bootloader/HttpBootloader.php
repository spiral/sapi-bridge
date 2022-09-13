<?php

declare(strict_types=1);

namespace App\Bootloader;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\Container\SingletonInterface;
use Spiral\Http\Config\HttpConfig;
use Spiral\Http\Http;
use Spiral\Http\Pipeline;

final class HttpBootloader extends Bootloader implements SingletonInterface
{
    protected const SINGLETONS = [
        Http::class => [self::class, 'httpCore'],
    ];

    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    public function init(): void
    {
        $this->config->setDefaults(
            HttpConfig::CONFIG,
            [
                'basePath'   => '/',
                'headers'    => [
                    'Content-Type' => 'text/html; charset=UTF-8',
                ],
                'middleware' => [],
                'chunkSize' => null,
                'inputBags' => [],
            ]
        );
    }

    protected function httpCore(
        HttpConfig $config,
        Pipeline $pipeline,
        RequestHandlerInterface $handler,
        ResponseFactoryInterface $responseFactory,
        ContainerInterface $container
    ): Http {
        $core = new Http($config, $pipeline, $responseFactory, $container);
        $core->setHandler($handler);

        return $core;
    }
}
