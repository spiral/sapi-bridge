<?php

declare(strict_types=1);

namespace App\Sapi;

use Psr\Http\Message\ResponseInterface;

final class BufferEmitter
{
    public ResponseInterface $response;

    public function emit(ResponseInterface $response): void
    {
        $this->response = $response;
    }
}
