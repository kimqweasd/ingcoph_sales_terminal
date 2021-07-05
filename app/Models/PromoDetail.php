<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'promo_id',
        'item_id',
        'type',
        'quantity',
        'free_of_charge',
        'created_at',
        'updated_at'
    ];

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
