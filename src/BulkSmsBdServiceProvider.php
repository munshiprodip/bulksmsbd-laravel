<?php

declare(strict_types=1);

namespace BulkSmsBd\Laravel;

use BulkSmsBd\Laravel\Services\BulkSmsBdClient;
use Illuminate\Support\ServiceProvider;

class BulkSmsBdServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/bulksmsbd.php',
            'bulksmsbd'
        );

        $this->app->singleton('bulksmsbd', function ($app) {
            $config = $app['config']['bulksmsbd'] ?? [];

            return new BulkSmsBd(
                apiKey: (string) ($config['api_key'] ?? ''),
                senderId: (string) ($config['sender_id'] ?? ''),
                baseUrl: (string) ($config['base_url'] ?? 'http://bulksmsbd.net'),
                timeout: (int) ($config['timeout'] ?? 15),
                throwExceptions: (bool) ($config['throw_exceptions'] ?? true)
            );
        });

        $this->app->alias('bulksmsbd', BulkSmsBd::class);
        $this->app->alias('bulksmsbd', BulkSmsBdClient::class);
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/bulksmsbd.php' => $this->app->configPath('bulksmsbd.php'),
            ], 'bulksmsbd-config');
        }
    }
}
