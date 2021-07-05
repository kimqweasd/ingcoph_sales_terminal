<?php

namespace App\Providers;

use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Promo;
use App\Models\PromoDetail;
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
            'items' => Item::class,
            'payment_methods' => PaymentMethod::class,
            'promos' => Promo::class,
            'promo_details' => PromoDetail::class
        ]);
    }
}
