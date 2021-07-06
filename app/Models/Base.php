<?php

namespace App\Models;

use App\Traits\MorphMap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class Base extends Model
{
    use HasFactory, MorphMap;
}
