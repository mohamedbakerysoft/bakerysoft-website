<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = [
        'name_ar',
        'slug_ar',
        'category_id',
        'description',
        'tool_type',
        'meta_title',
        'meta_description',
        'schema',
        'settings',
        'content',
        'faq',
        'is_featured',
    ];

    protected $casts = [
        'schema' => 'array',
        'settings' => 'array',
        'content' => 'array',
        'faq' => 'array',
        'is_featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function route(): string
    {
        return url('/' . $this->category->slug_ar . '/' . $this->slug_ar);
    }
}
