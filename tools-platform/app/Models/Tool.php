<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    public const TYPE_LABELS = [
        'compound_interest' => 'استثمار ونمو',
        'loan' => 'تمويل وقروض',
        'investment_return' => 'عائد استثماري',
        'stock_profit' => 'ربح الأسهم',
        'crypto_profit' => 'ربح العملات الرقمية',
        'profit_margin' => 'ربحية الأعمال',
        'vat' => 'ضرائب ورسوم',
        'discount' => 'خصومات وأسعار',
        'percentage' => 'نسب مئوية',
        'gold_value' => 'أسعار الذهب',
        'inflation' => 'تضخم وقوة شرائية',
        'salary' => 'الراتب والدخل',
        'age' => 'الحياة اليومية',
        'days_between' => 'تواريخ ومواعيد',
        'time_difference' => 'وقت وساعات',
        'bmi' => 'الصحة واللياقة',
        'savings_goal' => 'ادخار وأهداف',
        'break_even' => 'إدارة المشاريع',
        'unit_conversion_lookup' => 'تحويل الوحدات',
    ];

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

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->tool_type] ?? 'أداة عملية';
    }
}
