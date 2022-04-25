<?php

declare(strict_types=1);

namespace App\Bootloader;

use App\Controller\TestController;
use Spiral\Bootloader\DomainBootloader;
use Spiral\Router\Route;
use Spiral\Router\RouterInterface;
use Spiral\Router\Target\Controller;

final class AppBootloader extends DomainBootloader
{
    public function boot(RouterInterface $router): void
    {
        $route = new Route(
            '/<action>[/<name>]',
            new Controller(TestController::class)
        );

        $router->setDefault($route);
    }
}
