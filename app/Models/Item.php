<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;

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
}
