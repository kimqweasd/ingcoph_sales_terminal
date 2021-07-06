<?php

namespace App\Models;

use App\Enums\SyncType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Base
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'model',
        'name',
        'quantity',
        'srp'
    ];

    public function promos(): BelongsToMany
    {
        return $this->belongsToMany(
            Promo::class,
            'item_promo',
            'item_id',
            'promo_id'
        )->withPivot(['start_date', 'end_date']);
    }

    public function syncSettings(): array
    {
        return [
            'group' => [$this->getMorphMapKey(Item::class)],
            'type' => SyncType::PAGINATED,
            'callback' => function($module, $collection) {
                $module::insert($collection);
                return ['count' => $module::count()];
            }
        ];
    }
}
