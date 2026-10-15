<?php

namespace TALLKit\Tests;

use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use TALLKit\TALLKitServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Livewire keeps "a component has been rendered" in static state across tests.
        app('livewire')->flushState();
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('app.key', 'base64:2fl+Ktvkfl+Fuz4Qp/A75G2RTiWVA/ZoKZvp6fiiM10=');
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            TALLKitServiceProvider::class,
        ];
    }
}
