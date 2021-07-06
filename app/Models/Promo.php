<?php

namespace App\Models;

use App\Enums\SyncType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Promo extends Base
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'type',
        'spend',
        'discount_type',
        'discount_amount',
        'all_items',
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    public function promoDetails(): HasMany
    {
        return $this->hasMany(PromoDetail::class, 'promo_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            Item::class,
            'item_promo',
            'promo_id',
            'item_id'
        )->withPivot(['start_date', 'end_date']);
    }

    public function syncSettings(): array
    {
        return [
            'group' => [$this->getMorphMapKey(Promo::class), $this->getMorphMapKey(PromoDetail::class), 'item_promo'],
            'type' => SyncType::PAGINATED,
            'callback' => function($module, $collection) {
                foreach ($collection as $data) {

                    $module::create($data['promo']);

                    if (isset($data['details']) && !empty($data['details'])) {
                        foreach ($data['details'] as $detail) {
                            PromoDetail::create($detail);
                        }
                    }

                    if (isset($data['pivot']) && !empty($data['pivot'])) {
                        foreach ($data['pivot'] as $pivot) {
                            DB::table('item_promo')->insert([
                                'item_id' => (int)$pivot['item_id'],
                                'promo_id' => (int)$pivot['promo_id'],
                                'start_date' => Carbon::parse($pivot['start_date'])->startOfDay()->toDateTimeString(),
                                'end_date' => Carbon::parse($pivot['end_date'])->endOfDay()->toDateTimeString(),
                            ]);
                        }
                    }
                }

                return ['count' => $module::count()];
            }
        ];
    }
}
