<?php

namespace App\Providers;

use App\Services\Blueprint\SyncServiceInterface;
use App\Services\Concrete\SyncService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bindIf(SyncServiceInterface::class, SyncService::class);
    }
}
