<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Throwable;

class LiveCurrencyRateService
{
    private const CURRENCY_CODES = [
        'يورو' => 'EUR',
        'دولار أمريكي' => 'USD',
        'جنيه إسترليني' => 'GBP',
        'ريال سعودي' => 'SAR',
        'درهم إماراتي' => 'AED',
        'دينار كويتي' => 'KWD',
        'ريال قطري' => 'QAR',
        'دينار أردني' => 'JOD',
        'جنيه مصري' => 'EGP',
        'درهم مغربي' => 'MAD',
        'دينار بحريني' => 'BHD',
        'ليرة تركية' => 'TRY',
        'فرنك سويسري' => 'CHF',
        'ين ياباني' => 'JPY',
        'يوان صيني' => 'CNY',
        'روبية هندية' => 'INR',
        'دولار كندي' => 'CAD',
        'دولار أسترالي' => 'AUD',
        'دولار نيوزيلندي' => 'NZD',
        'دولار سنغافوري' => 'SGD',
        'ليف بلغاري' => 'BGN',
        'ريال برازيلي' => 'BRL',
        'كرونة سويدية' => 'SEK',
        'كرونة نرويجية' => 'NOK',
        'كرونة دنماركية' => 'DKK',
    ];

    public function getRateForArabicUnits(string $fromUnitAr, string $toUnitAr): ?array
    {
        $base = self::CURRENCY_CODES[$fromUnitAr] ?? null;
        $target = self::CURRENCY_CODES[$toUnitAr] ?? null;

        if (! $base || ! $target) {
            return null;
        }

        return $this->getRate($base, $target);
    }

    public function getRate(string $base, string $target): ?array
    {
        $base = strtoupper($base);
        $target = strtoupper($target);

        if ($base === '' || $target === '' || $base === $target) {
            return [
                'rate' => 1.0,
                'source' => 'identity',
                'fetched_at' => now(),
            ];
        }

        $cacheKey = "live_currency_rate:{$base}:{$target}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($base, $target) {
            try {
                $response = Http::timeout(4)
                    ->acceptJson()
                    ->get(config('services.exchange_rates.base_url') . '/latest/' . $base);

                if (! $response->successful()) {
                    return null;
                }

                if (data_get($response->json(), 'result') !== 'success') {
                    return null;
                }

                $rate = (float) data_get($response->json(), "rates.{$target}");

                if ($rate <= 0) {
                    return null;
                }

                return [
                    'rate' => $rate,
                    'source' => 'open-er-api',
                    'fetched_at' => now(),
                    'effective_date' => $this->parseDate(data_get($response->json(), 'time_last_update_utc')),
                    'base' => $base,
                    'target' => $target,
                ];
            } catch (Throwable) {
                return null;
            }
        });
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}
