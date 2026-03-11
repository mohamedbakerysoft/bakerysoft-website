<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    private const POPULAR_SLUGS = [
        'حاسبة-الفائدة-المركبة',
        'حاسبة-القرض',
        'حاسبة-العمر',
        'حاسبة-النسبة-المئوية',
        'حاسبة-الخصم',
        'حاسبة-ضريبة-القيمة-المضافة',
        'حاسبة-مؤشر-كتلة-الجسم',
        'حاسبة-الراتب',
        'حاسبة-الأيام-بين-تاريخين',
        'حاسبة-فرق-الوقت',
        'حاسبة-هدف-الادخار-الاستثماري',
        'حاسبة-العائد-السنوي-المركب',
        'حاسبة-ربح-الأسهم',
        'حاسبة-ربح-العملات-الرقمية',
        'حاسبة-هامش-الربح',
        'حاسبة-قيمة-الذهب',
        'حاسبة-التضخم',
        'حاسبة-خطة-الادخار',
        'حاسبة-نقطة-التعادل',
    ];

    private const POPULAR_TYPES = [
        'compound_interest',
        'loan',
        'age',
        'percentage',
        'discount',
        'vat',
        'investment_return',
        'salary',
        'days_between',
        'time_difference',
        'bmi',
        'stock_profit',
        'crypto_profit',
        'profit_margin',
        'gold_value',
        'inflation',
        'savings_goal',
        'break_even',
        'unit_conversion_lookup',
    ];

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

    public function scopePopular(Builder $query): Builder
    {
        $slugCase = 'CASE';
        $slugBindings = [];

        foreach (self::POPULAR_SLUGS as $index => $slug) {
            $slugCase .= ' WHEN slug_ar = ? THEN ' . $index;
            $slugBindings[] = $slug;
        }

        $slugCase .= ' ELSE ' . count(self::POPULAR_SLUGS) . ' END';

        $typeCase = 'CASE';
        $typeBindings = [];

        foreach (self::POPULAR_TYPES as $index => $type) {
            $typeCase .= ' WHEN tool_type = ? THEN ' . $index;
            $typeBindings[] = $type;
        }

        $typeCase .= ' ELSE ' . count(self::POPULAR_TYPES) . ' END';

        return $query
            ->orderByRaw($slugCase, $slugBindings)
            ->orderByDesc('is_featured')
            ->orderByRaw($typeCase, $typeBindings)
            ->orderBy('name_ar');
    }
}
