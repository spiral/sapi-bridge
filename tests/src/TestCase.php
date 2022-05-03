<?php

declare(strict_types=1);

namespace Tests;

use App\Bootloader\AppBootloader;
use App\TestApp;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Spiral\Boot\AbstractKernel;
use Spiral\Bootloader\ExceptionHandlerBootloader;
use Spiral\Http\Bootloader\DiactorosBootloader;
use Spiral\Bootloader\Http\ErrorHandlerBootloader;
use Spiral\Bootloader\Http\HttpBootloader;
use Spiral\Bootloader\Http\RouterBootloader;
use Spiral\Bootloader\SnapshotsBootloader;
use Spiral\Sapi\Bootloader\SapiBootloader;
use Spiral\Sapi\Emitter\SapiEmitter;
use Spiral\Testing\TestableKernelInterface;
use Spiral\Testing\TestCase as SpiralTesting;

abstract class TestCase extends SpiralTesting
{
    protected function createResponse(
        int $status = 200,
        array $headers = [],
        $body = null,
        string $version = '1.1'
    ): ResponseInterface {
        $response = (new Response())
            ->withStatus($status)
            ->withProtocolVersion($version);
        foreach ($headers as $header => $value) {
            $response = $response->withHeader($header, $value);
        }
        if ($body instanceof StreamInterface) {
            $response = $response->withBody($body);
        } elseif (is_string($body)) {
            $response->getBody()->write($body);
        }
        return $response;
    }

    protected function createEmitter(?int $bufferSize = null): SapiEmitter
    {
        $emitter = new SapiEmitter();
        if ($bufferSize !== null) {
            $emitter->bufferSize = $bufferSize;
        }
        return $emitter;
    }

    public function rootDirectory(): string
    {
        return \dirname(__DIR__) . '/app';
    }

    public function defineBootloaders(): array
    {
        return [
            SnapshotsBootloader::class,
            HttpBootloader::class,
            DiactorosBootloader::class,
            ErrorHandlerBootloader::class,
            RouterBootloader::class,
            SapiBootloader::class,
            ExceptionHandlerBootloader::class,
            AppBootloader::class,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanUpRuntimeDirectory();
    }

    /**
     * @return AbstractKernel|TestableKernelInterface
     */
    public function createAppInstance(): TestableKernelInterface
    {
        return TestApp::createWithBootloaders(
            $this->defineBootloaders(),
            $this->defineDirectories($this->rootDirectory()),
            false
        );
    }
}
