<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel\Tests;

use BulkSmsBd\Laravel\BulkSmsBdServiceProvider;
use BulkSmsBd\Laravel\Facades\BulkSmsBd;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Get package service providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            BulkSmsBdServiceProvider::class,
        ];
    }

    /**
     * Get package facade aliases.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'BulkSmsBd' => BulkSmsBd::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('bulksmsbd.api_key', 'test_api_key_123');
        $app['config']->set('bulksmsbd.sender_id', '8801700000000');
        $app['config']->set('bulksmsbd.base_url', 'https://bulksmsbd.net');
        $app['config']->set('bulksmsbd.throw_exceptions', true);
    }
}
