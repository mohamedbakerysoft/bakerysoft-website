<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    private const INDEXABLE_PAIRS = [
        'دولار-أمريكي__جنيه-مصري',
        'جنيه-مصري__دولار-أمريكي',
        'ريال-سعودي__جنيه-مصري',
        'جنيه-مصري__ريال-سعودي',
        'درهم-إماراتي__جنيه-مصري',
        'يورو__جنيه-مصري',
        'سنتيمتر__إنش',
        'إنش__سنتيمتر',
        'متر__قدم',
        'كيلومتر__متر',
    ];

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

    public static function indexablePairs(): array
    {
        return self::INDEXABLE_PAIRS;
    }

    public function pairKey(): string
    {
        return $this->from_slug_ar . '__' . $this->to_slug_ar;
    }

    public function isIndexable(): bool
    {
        return in_array($this->pairKey(), self::INDEXABLE_PAIRS, true);
    }

    public function scopeIndexable(Builder $query): Builder
    {
        $pairs = collect(self::INDEXABLE_PAIRS);

        return $query->where(function (Builder $builder) use ($pairs) {
            foreach ($pairs as $pair) {
                [$from, $to] = explode('__', $pair, 2);
                $builder->orWhere(function (Builder $subQuery) use ($from, $to) {
                    $subQuery->where('from_slug_ar', $from)
                        ->where('to_slug_ar', $to);
                });
            }
        });
    }
}
