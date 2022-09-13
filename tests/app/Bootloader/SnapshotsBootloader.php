<?php

declare(strict_types=1);

namespace App\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Exceptions\Renderer\PlainRenderer;
use Spiral\Exceptions\Verbosity;
use Spiral\Files\FilesInterface;
use Spiral\Snapshots\FileSnapshot;

final class SnapshotsBootloader extends Bootloader
{
    protected const SINGLETONS = [
        FileSnapshot::class => [self::class, 'fileSnapshot'],
    ];

    private function fileSnapshot(
        EnvironmentInterface $env,
        DirectoriesInterface $dirs,
        FilesInterface $files
    ): FileSnapshot {
        return new FileSnapshot(
            $dirs->get('runtime') . '/snapshots/',
            25,
            Verbosity::tryFrom((int)($env->get('SNAPSHOT_VERBOSITY') ?? Verbosity::VERBOSE->value)),
            new PlainRenderer(),
            $files
        );
    }
}
