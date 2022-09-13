<?php

declare(strict_types=1);

namespace App\Bootloader;

use App\Controller\TestController;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Router\Route;
use Spiral\Router\RouterInterface;
use Spiral\Router\Target\Controller;

final class AppBootloader extends Bootloader
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
