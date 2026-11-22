<?php

namespace App\Providers;

use App\Services\LarvasApi\HttpLarvasApiClient;
use App\Services\LarvasApi\LarvasApiClient;
use Illuminate\Support\ServiceProvider;

/**
 * Registra el cliente HTTP de API_larvas.
 */
class LarvasApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(LarvasApiClient::class, function ($app) {
            return new HttpLarvasApiClient(config('larvas_api'));
        });
    }
}
