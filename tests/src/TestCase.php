<?php

declare(strict_types=1);

namespace Tests;

use App\Bootloader\AppBootloader;
use App\Bootloader\ExceptionHandlerBootloader;
use App\Bootloader\HttpBootloader;
use App\Bootloader\RouterBootloader;
use App\Bootloader\SnapshotsBootloader;
use App\TestApp;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Spiral\Boot\AbstractKernel;
use Spiral\Core\Container;
use Spiral\Http\Config\HttpConfig;
use Spiral\Nyholm\Bootloader\NyholmBootloader;
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
        $this->getContainer()->bind(HttpConfig::class, new HttpConfig(['headers' => []]));

        $factory = $this->getContainer()->get(ResponseFactoryInterface::class);
        $response = $factory->createResponse($status)->withProtocolVersion($version);

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
            NyholmBootloader::class,
         //   ErrorHandlerBootloader::class,
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
    public function createAppInstance(Container $container = new Container()): TestableKernelInterface
    {
        return TestApp::create(
            $this->defineDirectories($this->rootDirectory()),
            false
        )->withBootloaders($this->defineBootloaders());
    }
}
