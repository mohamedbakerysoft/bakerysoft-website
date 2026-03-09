<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    protected $fillable = [
        'group_key',
        'from_unit_ar',
        'from_slug_ar',
        'to_unit_ar',
        'to_slug_ar',
        'ratio',
    ];

    protected $casts = [
        'ratio' => 'float',
    ];
}
