<?php

namespace App\Models;

use App\Enums\SyncType;

class Store extends Base
{
    protected $table = 'store';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'type',
        'address',
        'owner_name',
        'tin',
        'phone_number',
        'telephone_number',
        'email',
    ];

    public function syncSettings(): array
    {
        return [
            'group' => [$this->getMorphMapKey(Store::class)],
            'type' => SyncType::SINGLE,
            'callback' => function($module, $data) {
                return $module::create($data);
            }
        ];
    }
}
