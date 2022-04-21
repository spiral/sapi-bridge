<?php

declare(strict_types=1);

namespace Tests\Emitter;

include __DIR__ . '/../Support/httpFunctionMocks.php';

use Spiral\Http\Config\HttpConfig;
use Spiral\Sapi\Bootloader\SapiBootloader;
use Spiral\Sapi\Emitter\Exception\EmitterException;
use Spiral\Sapi\Emitter\SapiEmitter;
use Tests\Support\HTTPFunctions;
use Tests\Support\NotReadableStream;
use Tests\TestCase;

/**
 * @runInSeparateProcess
 */
final class SapiEmitterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        HTTPFunctions::reset();
    }

    public static function tearDownAfterClass(): void
    {
        HTTPFunctions::reset();
    }

    public function testEmit(): void
    {
        $body = 'Example body';
        $response = $this->createResponse(200, ['X-Test' => 1], $body);

        $this->createEmitter()->emit($response);

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertContains('X-Test: 1', $this->getHeaders());
        $this->expectOutputString($body);
    }

    public function testEmitWithoutStream(): void
    {
        $body = 'Example body';
        $response = $this->createResponse(200, ['X-Test' => 1], $body);

        $this->createEmitter(0)->emit($response);

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertContains('X-Test: 1', $this->getHeaders());
        $this->expectOutputString($body);
    }

    public function testEmitterWithNotReadableStream(): void
    {
        $body = new NotReadableStream();
        $response = $this->createResponse(200, ['X-Test' => 42], $body);

        $this->createEmitter()->emit($response);

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertCount(1, $this->getHeaders());
        $this->assertContains('X-Test: 42', $this->getHeaders());
    }

    public function testContentLengthNotOverwrittenIfPresent(): void
    {
        $length = 100;
        $response = $this->createResponse(200, ['Content-Length' => $length, 'X-Test' => 1], 'Example body');

        $this->createEmitter()->emit($response);

        $this->assertEquals(200, $this->getResponseCode());
        $this->assertCount(2, $this->getHeaders());
        $this->assertContains('X-Test: 1', $this->getHeaders());
        $this->assertContains('Content-Length: ' . $length, $this->getHeaders());
        $this->expectOutputString('Example body');
    }

    public function testContentFullyEmitted(): void
    {
        $body = 'Example body';
        $response = $this->createResponse(200, ['Content-length' => 1, 'X-Test' => 1], $body);

        $this->createEmitter()->emit($response);

        $this->expectOutputString($body);
    }

    public function testSentHeadersRemoved(): void
    {
        HTTPFunctions::header('Cookie-Set: First Cookie');
        HTTPFunctions::header('X-Test: 1');
        $body = 'Example body';
        $response = $this->createResponse(200, [], $body);

        $this->createEmitter()->emit($response);

        $this->assertEquals([
            'Cookie-Set: First Cookie',
            'X-Test: 1',
        ], $this->getHeaders());
        $this->expectOutputString($body);
    }

    public function testExceptionWhenHeadersHaveBeenSent(): void
    {
        $body = 'Example body';
        $response = $this->createResponse(200, [], $body);
        HTTPFunctions::set_headers_sent(true, 'test-file.php', 200);

        $this->expectException(EmitterException::class);
        $this->expectExceptionMessage('Unable to emit response, headers already send.');

        $this->createEmitter()->emit($response);
    }

    public function testExceptionBufferWithData(): void
    {
        ob_start();
        $body = 'Example body';
        $response = $this->createResponse(200, [], $body);

        $this->expectException(EmitterException::class);
        $this->expectExceptionMessage('Unable to emit response, found non closed buffered output.');

        try {
            echo 'some data';
            $this->createEmitter()->emit($response);
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            \ob_end_clean();
        }
    }

    public function testEmitDuplicateHeaders(): void
    {
        $body = 'Example body';
        $response = $this->createResponse(200, [], $body)
                         ->withHeader('X-Test', '1')
                         ->withAddedHeader('X-Test', '2')
                         ->withAddedHeader('X-Test', '3; 3.5')
                         ->withHeader('Cookie-Set', '1')
                         ->withAddedHeader('cookie-Set', '2')
                         ->withAddedHeader('Cookie-set', '3');

        (new SapiEmitter())->emit($response);
        $this->assertEquals(200, $this->getResponseCode());
        $this->assertContains('X-Test: 1', $this->getHeaders());
        $this->assertContains('X-Test: 2', $this->getHeaders());
        $this->assertContains('X-Test: 3; 3.5', $this->getHeaders());
        $this->assertContains('Cookie-Set: 1', $this->getHeaders());
        $this->assertContains('Cookie-Set: 2', $this->getHeaders());
        $this->assertContains('Cookie-Set: 3', $this->getHeaders());
        $this->expectOutputString($body);
    }

    public function testDefaultChunkSize(): void
    {
        $bootloader = new SapiBootloader();

        $emitter = $bootloader->createEmitter(new HttpConfig(['chunkSize' => null]));

        $this->assertSame($emitter->bufferSize, 2_097_152);
    }

    /* TODO waiting code from v2.13
    public function testChunkSize(): void
    {
        $bootloader = new SapiBootloader();

        $emitter = $bootloader->createEmitter(new HttpConfig(['chunkSize' => 100]));
        $this->assertSame($emitter->bufferSize, 100);

        $emitter = $bootloader->createEmitter(new HttpConfig(['chunkSize' => '100']));
        $this->assertSame($emitter->bufferSize, 100);

        $emitter = $bootloader->createEmitter(new HttpConfig(['chunkSize' => 0]));
        $this->assertSame($emitter->bufferSize, 0);

        // value less than 0. Should be a default value
        $emitter = $bootloader->createEmitter(new HttpConfig(['chunkSize' => -1]));
        $this->assertSame($emitter->bufferSize, 2_097_152);
    }
    */

    private function getHeaders(): array
    {
        return HTTPFunctions::headers_list();
    }

    private function getResponseCode(): int
    {
        return HTTPFunctions::http_response_code();
    }
}
