<?php

namespace App\Providers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class EloquentRelationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'user' => User::class,
            'store' => Store::class,
            //'items'
            //'promos'
        ]);
    }
}
