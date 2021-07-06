<?php

namespace App\Models;

use App\Enums\SyncType;
use App\Traits\MorphMap;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, MorphMap;

    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'store_id',
        'name',
        'active',
        'locked',
        'manager',
        'super_admin'
    ];

    public function syncSettings(): array
    {
        return [
            'group' => [$this->getMorphMapKey(User::class)],
            'type' => SyncType::SINGLE,
            'callback' => function($module, $data) {
                return $module::create($data);
            }
        ];
    }
}
