<?php

namespace App\Models;

use App\Enums\SyncType;

class PaymentMethod extends Base
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'model',
        'remarks'
    ];

    public function syncSettings(): array
    {
        return [
            'group' => [$this->getMorphMapKey(PaymentMethod::class)],
            'type' => SyncType::PAGINATED,
            'callback' => function($module, $collection) {
                $module::insert($collection);
                return ['count' => $module::count()];
            }
        ];
    }
}
