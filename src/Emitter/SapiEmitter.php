<?php

declare(strict_types=1);

namespace Spiral\Sapi\Emitter;

use Psr\Http\Message\ResponseInterface;
use Spiral\Sapi\Emitter\Exception\EmitterException;

/**
 * Source code has been extracted from Zend/Diactoros.
 */
final class SapiEmitter
{
    /**
     * Preferred chunk size to be read from the stream before emitting. A value of 0 disables stream response.
     */
    public int $bufferSize = 2_097_152; // 2MB

    /**
     * Emits a response for a PHP SAPI environment.
     *
     * Emits the status line and headers via the header() function, and the
     * body content via the output buffer.
     */
    public function emit(ResponseInterface $response): bool
    {
        $this->assertNoPreviousOutput();

        $this->emitHeaders($response);
        $this->emitStatusLine($response);
        $this->emitBody($response);

        return true;
    }

    /**
     * Emit the message body.
     */
    private function emitBody(ResponseInterface $response): void
    {
        $body = $response->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }
        if (!$body->isReadable()) {
            return;
        }
        if ($this->bufferSize === 0) {
            echo $body;
        }
        while (!$body->eof()) {
            echo $body->read($this->bufferSize);
            flush();
        }
    }

    /**
     * Checks to see if content has previously been sent.
     *
     * If either headers have been sent or the output buffer contains content,
     * raises an exception.
     *
     * @throws EmitterException if headers have already been sent.
     * @throws EmitterException if output is present in the output buffer.
     */
    private function assertNoPreviousOutput(): void
    {
        if (headers_sent()) {
            throw new EmitterException('Unable to emit response, headers already send.');
        }

        if (\ob_get_level() > 0 && \ob_get_length() > 0) {
            throw new EmitterException('Unable to emit response, found non closed buffered output.');
        }
    }

    /**
     * Emit the status line.
     *
     * Emits the status line using the protocol version and status code from
     * the response; if a reason phrase is available, it, too, is emitted.
     *
     * It is important to mention that this method should be called after
     * `emitHeaders()` in order to prevent PHP from changing the status code of
     * the emitted response.
     */
    private function emitStatusLine(ResponseInterface $response): void
    {
        $reasonPhrase = $response->getReasonPhrase();
        $statusCode = $response->getStatusCode();

        header(\sprintf(
            'HTTP/%s %d%s',
            $response->getProtocolVersion(),
            $statusCode,
            ($reasonPhrase ? ' ' . $reasonPhrase : '')
        ), true, $statusCode);
    }

    /**
     * Emit response headers.
     *
     * Loops through each header, emitting each; if the header value
     * is an array with multiple values, ensures that each is sent
     * in such a way as to create aggregate headers (instead of replace
     * the previous).
     */
    private function emitHeaders(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        foreach ($response->getHeaders() as $header => $values) {
            $name = $this->filterHeader($header);
            $first = $name === 'Set-Cookie' ? false : true;
            foreach ($values as $value) {
                header(\sprintf(
                    '%s: %s',
                    $name,
                    $value
                ), $first, $statusCode);
                $first = false;
            }
        }
    }

    /**
     * Filter a header name to wordcase
     */
    private function filterHeader(string $header): string
    {
        return \ucwords($header, '-');
    }
}
