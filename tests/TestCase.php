<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create the application.
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(ConsoleKernel::class)->bootstrap();

        return $app;
    }
}
