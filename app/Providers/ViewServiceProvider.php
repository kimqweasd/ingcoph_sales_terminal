<?php

namespace App\Providers;

use App\Models\User;
use App\Traits\SessionHandler;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;

class ViewServiceProvider extends ServiceProvider
{
    use SessionHandler;

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
        View::composer(['*'], function($view){
            $view->with([
                'auth' => [
                    'api' => $this->getIfExists('api'),
                    'access_token' => $this->getIfExists('access_token'),
                    'refresh_token' => $this->getIfExists('refresh_token')
                ],
                'shared' => [
                    'user' => User::first(),
                    'salesTerminal' => [

                        //Suggest Syncing
                        'synced' => false
                    ]
                ]
            ]);
        });
    }
}
