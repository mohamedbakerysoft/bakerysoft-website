<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name_ar',
        'slug_ar',
        'headline',
        'description',
    ];

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }
}
