<?php

declare(strict_types=1);

namespace App\Bootloader;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\Core;
use Spiral\Core\CoreInterface;
use Spiral\Core\Exception\ScopeException;
use Spiral\Http\Config\HttpConfig;
use Spiral\Router\RouteInterface;
use Spiral\Router\Router;
use Spiral\Router\RouterInterface;
use Spiral\Router\UriHandler;

final class RouterBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        HttpBootloader::class,
    ];

    protected const SINGLETONS = [
        CoreInterface::class => Core::class,
        RouterInterface::class => [self::class, 'router'],
        RouteInterface::class => [self::class, 'route'],
        RequestHandlerInterface::class => RouterInterface::class
    ];

    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    private function router(
        UriHandler $uriHandler,
        ContainerInterface $container,
        ?EventDispatcherInterface $dispatcher = null
    ): RouterInterface {
        return new Router(
            $this->config->getConfig(HttpConfig::CONFIG)['basePath'],
            $uriHandler,
            $container,
            $dispatcher
        );
    }

    private function route(ServerRequestInterface $request): RouteInterface
    {
        $route = $request->getAttribute(Router::ROUTE_ATTRIBUTE, null);
        if ($route === null) {
            throw new ScopeException('Unable to resolve Route, invalid request scope');
        }

        return $route;
    }
}
