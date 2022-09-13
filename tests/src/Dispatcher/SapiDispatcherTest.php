<?php

declare(strict_types=1);

namespace Tests\Dispatcher;

use App\Sapi\BufferEmitter;
use Psr\Http\Message\ResponseInterface;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Files\FilesInterface;
use Spiral\Sapi\Dispatcher\SapiDispatcher;
use Tests\TestCase;

final class SapiDispatcherTest extends TestCase
{
    public function testCantServe(): void
    {
        $this->assertFalse($this->getContainer()->get(SapiDispatcher::class)->canServe());
    }

    public function testDispatch(): void
    {
        $emitter = new BufferEmitter();

        $_SERVER['REQUEST_URI'] = '/index';

        $this->getContainer()
            ->get(SapiDispatcher::class)
            ->serve(\Closure::bind(function (ResponseInterface $response) {$this->emit($response);}, $emitter));

        $this->assertSame('Hello, Dave.', (string) $emitter->response->getBody());
    }

    public function testDispatchError(): void
    {
        $emitter = new BufferEmitter();

        $this->initApp([
            'DEBUG' => true
        ]);

        $files = $this->getApp()->getContainer()->get(FilesInterface::class)->getFiles(
            $this->getApp()->getContainer()->get(DirectoriesInterface::class)->get('runtime') . '/snapshots/'
        );

        $this->assertCount(0, $files);

        $_SERVER['REQUEST_URI'] = '/error';
        $this->getApp()->getContainer()->get(SapiDispatcher::class)->serve(
            \Closure::bind(function (ResponseInterface $response) {$this->emit($response);}, $emitter)
        );

        $files = $this->getApp()->getContainer()->get(FilesInterface::class)->getFiles(
            $this->getApp()->getContainer()->get(DirectoriesInterface::class)->get('runtime') . '/snapshots/'
        );

        $this->assertCount(1, $files);

        $this->assertStringContainsString('undefined', (string) $emitter->response->getBody());
    }
}
