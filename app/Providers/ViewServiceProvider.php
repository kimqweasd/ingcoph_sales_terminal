<?php

namespace App\Providers;

use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Promo;
use App\Models\Store;
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
                    'store' => Store::first(),
                    'items' => [
                        'count' => Item::count()
                    ],
                    'payment_methods' => [
                        'count' => PaymentMethod::count()
                    ],
                    'promos' => [
                        'count' => Promo::count()
                    ],
                    'sales_terminal' => [
                        //Suggest Syncing
                        'synced' => false
                    ]
                ]
            ]);
        });
    }
}
