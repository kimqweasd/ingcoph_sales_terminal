<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    use HasFactory;

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

    public function itemInventories(): BelongsToMany
    {
        return $this->belongsToMany(
            Item::class,
            'item_promo',
            'promo_id',
            'item_id'
        )->withPivot(['start_date', 'end_date']);
    }
}
