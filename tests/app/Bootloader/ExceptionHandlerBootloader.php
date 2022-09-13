<?php

declare(strict_types=1);

namespace App\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Exceptions\ExceptionHandlerInterface;
use App\Exceptions\FileReporter;
use Spiral\Http\ErrorHandler\PlainRenderer;
use Spiral\Http\ErrorHandler\RendererInterface;
use Spiral\Http\Middleware\ErrorHandlerMiddleware\EnvSuppressErrors;
use Spiral\Http\Middleware\ErrorHandlerMiddleware\SuppressErrorsInterface;

final class ExceptionHandlerBootloader extends Bootloader
{
    protected const BINDINGS = [
        SuppressErrorsInterface::class => EnvSuppressErrors::class,
        RendererInterface::class => PlainRenderer::class,
    ];

    public function boot(ExceptionHandlerInterface $handler, FileReporter $files,): void
    {
        $handler->addReporter($files);
    }
}
