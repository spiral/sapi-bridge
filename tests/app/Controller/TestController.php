<?php

declare(strict_types=1);

namespace App\Controller;

class TestController
{
    public function index()
    {
        return 'Hello, Dave.';
    }

    public function error(): void
    {
        echo $undefined;
    }
}
