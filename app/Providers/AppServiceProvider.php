<?php

namespace App\Providers;

use App\Services\AuditoriaService;
use App\Services\BackupService;
use App\Services\ThisCodeWorksService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuditoriaService::class);
        $this->app->singleton(BackupService::class);
        $this->app->singleton(ThisCodeWorksService::class);
    }
}