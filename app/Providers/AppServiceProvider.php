<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $host = request()->getHost();

        if ($host && str_ends_with($host, '.app.github.dev')) {
            URL::forceRootUrl('https://' . $host);
            URL::forceScheme('https');
        }
    }
}
