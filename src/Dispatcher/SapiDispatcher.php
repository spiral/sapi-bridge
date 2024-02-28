<?php

declare(strict_types=1);

namespace Spiral\Sapi\Dispatcher;

use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\Attribute\DispatcherScope;
use Spiral\Boot\DispatcherInterface;
use Spiral\Boot\FinalizerInterface;
use Spiral\Exceptions\ExceptionHandlerInterface;
use Spiral\Framework\Spiral;
use Spiral\Http\Http;
use Spiral\Sapi\Dispatcher\Exception\InvalidEmitterException;
use Spiral\Sapi\Emitter\SapiEmitter;

#[DispatcherScope(Spiral::Http)]
final class SapiDispatcher implements DispatcherInterface
{
    public function __construct(
        private readonly FinalizerInterface $finalizer,
        private readonly ContainerInterface $container,
        private readonly ExceptionHandlerInterface $errorHandler,
    ) {
    }

    public function canServe(): bool
    {
        return PHP_SAPI !== 'cli';
    }

    public function serve(\Closure $emitter = null): void
    {
        // On demand to save some memory.

        // Disable buffering
        while (\ob_get_level() > 0) {
            \ob_end_flush();
        }

        /** @var Http $http */
        $http = $this->container->get(Http::class);

        /** @var \Closure $emitter */
        $emitter ??= \Closure::bind(function (ResponseInterface $response) {
            if (!\method_exists($this, 'emit')) {
                throw new InvalidEmitterException();
            }
            $this->emit($response);
        }, $this->container->get(SapiEmitter::class));

        try {
            $emitter($http->handle($this->initRequest()));
        } catch (\Throwable $e) {
            $this->handleException($emitter, $e);
        } finally {
            $this->finalizer->finalize(false);
        }
    }

    protected function initRequest(): ServerRequestInterface
    {
        return $this->container->get(ServerRequestCreatorInterface::class)->fromGlobals();
    }

    protected function handleException(\Closure $emitter, \Throwable $e): void
    {
        $this->errorHandler->report($e);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->container->get(ResponseFactoryInterface::class);
        $response = $responseFactory->createResponse(500);

        // Reporting system (non handled) exception directly to the client
        $response->getBody()->write(
            $this->errorHandler->render($e, format: 'html')
        );

        $emitter($response);
    }
}
