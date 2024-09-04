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
    private int $obLevel = 0;


    public function testCantServe(): void
    {
        $this->assertFalse(SapiDispatcher::canServe());
    }

    public function testDispatch(): void
    {
        $emitter = new BufferEmitter();

        $_SERVER['REQUEST_URI'] = '/index';

        $this->getContainer()
            ->get(SapiDispatcher::class)
            ->serve(\Closure::bind(function (ResponseInterface $response) {$this->emit($response);}, $emitter));

        $this->assertSame('Hello, Dave.', (string) $emitter->response->getBody());
        self::assertSame(0, \ob_get_level());
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
        self::assertSame(0, \ob_get_level());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->obLevel = \ob_get_level();
    }

    protected function tearDown(): void
    {
        // Restore OB level
        // SAPI Emitter resets OB level to 0 and does not restore it
        while (\ob_get_level() < $this->obLevel) {
            \ob_start();
        }

        parent::tearDown();
    }
}
