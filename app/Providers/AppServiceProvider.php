<?php

namespace App\Providers;

use App\Services\AuditoriaService;
use App\Services\BackupService;
use App\Services\ThisCodeWorksService;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuditoriaService::class);
        $this->app->singleton(BackupService::class);
        $this->app->singleton(ThisCodeWorksService::class);
    }

    public function boot(UrlGenerator $url): void
    {
        if ($this->app->environment('production')) {
            $url->forceScheme('https');
        }
    }
}