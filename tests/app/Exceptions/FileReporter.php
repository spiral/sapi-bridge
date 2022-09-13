<?php

declare(strict_types=1);

namespace App\Exceptions;

use Spiral\Exceptions\ExceptionReporterInterface;
use Spiral\Snapshots\FileSnapshot;

class FileReporter implements ExceptionReporterInterface
{
    public function __construct(
        private FileSnapshot $fileSnapshot,
    ) {
    }

    public function report(\Throwable $exception): void
    {
        $this->fileSnapshot->create($exception);
    }
}
