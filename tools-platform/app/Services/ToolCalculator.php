<?php

namespace App\Services;

use App\Models\Conversion;
use App\Models\Tool;
use Carbon\Carbon;

class ToolCalculator
{
    private const CURRENCIES = [
        'EGP' => ['label' => 'جنيه مصري', 'symbol' => 'ج.م'],
        'SAR' => ['label' => 'ريال سعودي', 'symbol' => 'ر.س'],
        'AED' => ['label' => 'درهم إماراتي', 'symbol' => 'د.إ'],
        'USD' => ['label' => 'دولار أمريكي', 'symbol' => '$'],
        'EUR' => ['label' => 'يورو', 'symbol' => '€'],
        'KWD' => ['label' => 'دينار كويتي', 'symbol' => 'د.ك'],
    ];

    public function calculate(Tool $tool, array $input): array
    {
        return match ($tool->tool_type) {
            'compound_interest' => $this->compoundInterest($input),
            'loan' => $this->loan($input),
            'investment_return' => $this->investmentReturn($input),
            'stock_profit', 'crypto_profit' => $this->tradeProfit($input),
            'profit_margin' => $this->profitMargin($input),
            'vat' => $this->vat($input),
            'discount' => $this->discount($input),
            'percentage' => $this->percentage($input),
            'gold_value' => $this->goldValue($input),
            'inflation' => $this->inflation($input),
            'salary' => $this->salary($input),
            'age' => $this->age($input),
            'days_between' => $this->daysBetween($input),
            'time_difference' => $this->timeDifference($input),
            'bmi' => $this->bmi($input),
            'savings_goal' => $this->savingsGoal($input),
            'break_even' => $this->breakEven($input),
            'unit_conversion_lookup' => $this->inlineUnitConversion($tool, $input),
            default => [],
        };
    }

    public function convertPage(Conversion $conversion, float $amount, ?array $liveRate = null): array
    {
        $effectiveRatio = (float) ($liveRate['rate'] ?? $conversion->ratio);
        $result = $amount * $effectiveRatio;
        $reverseRatio = $effectiveRatio > 0 ? (1 / $effectiveRatio) : 0;
        $usingLiveRate = ($liveRate['source'] ?? null) === 'open-er-api';
        $fetchedAt = data_get($liveRate, 'fetched_at');
        $effectiveDate = data_get($liveRate, 'effective_date');
        $rateNote = $usingLiveRate
            ? 'سعر حي مرجعي محدث عند الطلب'
            : 'سعر مرجعي محفوظ كبديل احتياطي';

        return [
            'rows' => [
                ['label' => 'القيمة المدخلة', 'value' => $this->format($amount)],
                ['label' => 'سعر التحويل', 'value' => '1 ' . $conversion->from_unit_ar . ' = ' . $this->format($effectiveRatio) . ' ' . $conversion->to_unit_ar],
                ['label' => 'الناتج', 'value' => $this->format($result) . ' ' . $conversion->to_unit_ar],
                ['label' => 'السعر العكسي', 'value' => '1 ' . $conversion->to_unit_ar . ' = ' . $this->format($reverseRatio) . ' ' . $conversion->from_unit_ar],
                ['label' => 'مصدر السعر', 'value' => $rateNote],
                ['label' => 'آخر تحديث', 'value' => $effectiveDate ? $effectiveDate->translatedFormat('j F Y - H:i') : ($fetchedAt ? $fetchedAt->translatedFormat('j F Y - H:i') : 'غير متاح')],
            ],
            'summary' => 'كل ' . $this->format($amount) . ' ' . $conversion->from_unit_ar . ' تساوي ' . $this->format($result) . ' ' . $conversion->to_unit_ar . ' وفق معدل تحويل ' . ($usingLiveRate ? 'حي مرجعي' : 'احتياطي') . ' قدره ' . $this->format($effectiveRatio) . '.',
        ];
    }

    private function compoundInterest(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $principal = (float) ($input['principal'] ?? 0);
        $rate = ((float) ($input['rate'] ?? 0)) / 100;
        $years = (float) ($input['years'] ?? 0);
        $monthlyContribution = (float) ($input['monthly_contribution'] ?? 0);
        $compounds = max((int) ($input['compounds_per_year'] ?? 12), 1);

        $balance = $principal;
        $points = [];
        $periods = (int) round($years * $compounds);
        $periodRate = $rate / $compounds;
        $perPeriodContribution = $monthlyContribution * (12 / $compounds);

        for ($i = 1; $i <= $periods; $i++) {
            $balance = ($balance + $perPeriodContribution) * (1 + $periodRate);
            if ($i % $compounds === 0) {
                $points[] = [
                    'label' => 'السنة ' . (int) ($i / $compounds),
                    'value' => round($balance, 2),
                ];
            }
        }

        $contributed = $principal + ($monthlyContribution * 12 * $years);
        $profit = $balance - $contributed;
        $growth = $contributed > 0 ? ($profit / $contributed) * 100 : 0;
        $profitShare = $balance > 0 ? ($profit / $balance) * 100 : 0;
        $avgAnnualGain = $years > 0 ? ($profit / $years) : 0;
        $doubleYears = $rate > 0 ? 72 / ($rate * 100) : null;

        return [
            'rows' => [
                ['label' => 'القيمة المستقبلية', 'value' => $this->money($balance, $currency)],
                ['label' => 'إجمالي المساهمات', 'value' => $this->money($contributed, $currency)],
                ['label' => 'الأرباح المتوقعة', 'value' => $this->money($profit, $currency)],
                ['label' => 'نسبة النمو الكلية', 'value' => $this->percent($growth)],
                ['label' => 'نسبة الأرباح من الرصيد النهائي', 'value' => $this->percent($profitShare)],
                ['label' => 'متوسط الربح السنوي', 'value' => $this->money($avgAnnualGain, $currency)],
                ['label' => 'المدة التقريبية لمضاعفة المال', 'value' => $doubleYears ? number_format($doubleYears, 1) . ' سنة' : 'غير متاح'],
            ],
            'summary' => 'إذا استثمرت ' . $this->money($principal, $currency) . ' مع إضافة شهرية قدرها ' . $this->money($monthlyContribution, $currency) . ' وبمعدل ' . (($rate * 100)) . '% لمدة ' . $years . ' سنة، فالقيمة المتوقعة هي ' . $this->money($balance, $currency) . ' مع نمو كلي يقارب ' . $this->percent($growth) . '.',
            'chart' => $points,
        ];
    }

    private function loan(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $amount = (float) ($input['amount'] ?? 0);
        $annualRate = ((float) ($input['rate'] ?? 0)) / 100;
        $years = (float) ($input['years'] ?? 0);
        $months = max((int) round($years * 12), 1);
        $monthlyRate = $annualRate / 12;

        $payment = $monthlyRate > 0
            ? ($amount * $monthlyRate) / (1 - pow(1 + $monthlyRate, -$months))
            : $amount / $months;

        $total = $payment * $months;
        $interest = $total - $amount;
        $interestShare = $total > 0 ? ($interest / $total) * 100 : 0;
        $annualPayments = $payment * 12;
        $minimumIncome = $payment / 0.35;

        return [
            'rows' => [
                ['label' => 'القسط الشهري', 'value' => $this->money($payment, $currency)],
                ['label' => 'إجمالي السداد', 'value' => $this->money($total, $currency)],
                ['label' => 'إجمالي الفوائد', 'value' => $this->money($interest, $currency)],
                ['label' => 'مدة السداد', 'value' => $this->integer($months) . ' شهر'],
                ['label' => 'القسط السنوي', 'value' => $this->money($annualPayments, $currency)],
                ['label' => 'نسبة الفوائد من إجمالي السداد', 'value' => $this->percent($interestShare)],
                ['label' => 'دخل شهري مريح للقسط', 'value' => $this->money($minimumIncome, $currency)],
            ],
            'summary' => 'قرض بقيمة ' . $this->money($amount, $currency) . ' لمدة ' . $years . ' سنة يحتاج إلى قسط شهري يقارب ' . $this->money($payment, $currency) . '، بينما يصل إجمالي الفوائد إلى ' . $this->money($interest, $currency) . '.',
        ];
    }

    private function investmentReturn(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $initial = (float) ($input['initial_amount'] ?? 0);
        $final = (float) ($input['final_amount'] ?? 0);
        $months = max((float) ($input['period_months'] ?? 1), 1);
        $profit = $final - $initial;
        $roi = $initial > 0 ? ($profit / $initial) * 100 : 0;
        $annualized = $initial > 0 ? ((pow(max($final, 0.01) / max($initial, 0.01), 12 / $months) - 1) * 100) : 0;
        $avgMonthlyProfit = $profit / $months;
        $monthlyReturn = $initial > 0 ? (($profit / $initial) / $months) * 100 : 0;
        $multiple = $initial > 0 ? number_format($final / $initial, 2) . 'x' : 'غير متاح';

        return [
            'rows' => [
                ['label' => 'الربح الصافي', 'value' => $this->money($profit, $currency)],
                ['label' => 'نسبة العائد', 'value' => $this->percent($roi)],
                ['label' => 'العائد السنوي التقريبي', 'value' => $this->percent($annualized)],
                ['label' => 'متوسط الربح الشهري', 'value' => $this->money($avgMonthlyProfit, $currency)],
                ['label' => 'متوسط العائد الشهري', 'value' => $this->percent($monthlyReturn)],
                ['label' => 'القيمة النهائية مقارنة بالبداية', 'value' => $multiple],
            ],
            'summary' => 'ارتفع الاستثمار من ' . $this->money($initial, $currency) . ' إلى ' . $this->money($final, $currency) . ' بعائد إجمالي ' . $this->percent($roi) . ' ومتوسط ربح شهري يقارب ' . $this->money($avgMonthlyProfit, $currency) . '.',
        ];
    }

    private function tradeProfit(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $quantity = (float) ($input['quantity'] ?? 0);
        $buy = (float) ($input['buy_price'] ?? 0);
        $sell = (float) ($input['sell_price'] ?? 0);
        $fees = (float) ($input['fees'] ?? 0);
        $cost = ($quantity * $buy) + $fees;
        $revenue = ($quantity * $sell) - $fees;
        $profit = $revenue - $cost;
        $margin = $cost > 0 ? ($profit / $cost) * 100 : 0;
        $profitPerUnit = $quantity > 0 ? $profit / $quantity : 0;
        $breakEvenSell = $quantity > 0 ? (($quantity * $buy) + (2 * $fees)) / $quantity : 0;

        return [
            'rows' => [
                ['label' => 'تكلفة الشراء', 'value' => $this->money($cost, $currency)],
                ['label' => 'قيمة البيع', 'value' => $this->money($revenue, $currency)],
                ['label' => 'صافي الربح', 'value' => $this->money($profit, $currency)],
                ['label' => 'نسبة الربح', 'value' => $this->percent($margin)],
                ['label' => 'الربح لكل وحدة', 'value' => $this->money($profitPerUnit, $currency)],
                ['label' => 'سعر التعادل للوحدة', 'value' => $this->money($breakEvenSell, $currency)],
            ],
            'summary' => 'صافي الربح من الصفقة يساوي ' . $this->money($profit, $currency) . ' بنسبة ' . $this->percent($margin) . '، بينما يكون سعر التعادل التقريبي للوحدة ' . $this->money($breakEvenSell, $currency) . '.',
        ];
    }

    private function profitMargin(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $revenue = (float) ($input['revenue'] ?? 0);
        $cost = (float) ($input['cost'] ?? 0);
        $profit = $revenue - $cost;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
        $markup = $cost > 0 ? ($profit / $cost) * 100 : 0;

        return [
            'rows' => [
                ['label' => 'الربح', 'value' => $this->money($profit, $currency)],
                ['label' => 'هامش الربح', 'value' => $this->percent($margin)],
                ['label' => 'نسبة الزيادة على التكلفة', 'value' => $this->percent($markup)],
                ['label' => 'التكلفة كنسبة من الإيراد', 'value' => $this->percent($revenue > 0 ? ($cost / $revenue) * 100 : 0)],
            ],
            'summary' => 'عند إيرادات ' . $this->money($revenue, $currency) . ' وتكلفة ' . $this->money($cost, $currency) . ' يصبح هامش الربح ' . $this->percent($margin) . ' ونسبة الزيادة على التكلفة ' . $this->percent($markup) . '.',
        ];
    }

    private function vat(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $amount = (float) ($input['amount'] ?? 0);
        $rate = (float) ($input['rate'] ?? 0);
        $vat = $amount * ($rate / 100);
        $total = $amount + $vat;

        return [
            'rows' => [
                ['label' => 'السعر قبل الضريبة', 'value' => $this->money($amount, $currency)],
                ['label' => 'قيمة الضريبة', 'value' => $this->money($vat, $currency)],
                ['label' => 'الإجمالي بعد الضريبة', 'value' => $this->money($total, $currency)],
                ['label' => 'نسبة الضريبة من الإجمالي', 'value' => $this->percent($total > 0 ? ($vat / $total) * 100 : 0)],
            ],
            'summary' => 'ضريبة القيمة المضافة على ' . $this->money($amount, $currency) . ' بنسبة ' . $rate . '% تساوي ' . $this->money($vat, $currency) . ' ليصبح الإجمالي ' . $this->money($total, $currency) . '.',
        ];
    }

    private function discount(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $price = (float) ($input['original_price'] ?? 0);
        $rate = (float) ($input['discount_rate'] ?? 0);
        $discount = $price * ($rate / 100);
        $final = $price - $discount;

        return [
            'rows' => [
                ['label' => 'السعر الأصلي', 'value' => $this->money($price, $currency)],
                ['label' => 'قيمة الخصم', 'value' => $this->money($discount, $currency)],
                ['label' => 'السعر بعد الخصم', 'value' => $this->money($final, $currency)],
                ['label' => 'النسبة التي ستدفعها فعليًا', 'value' => $this->percent(max(0, 100 - $rate))],
            ],
            'summary' => 'بعد خصم ' . $rate . '% ستوفر ' . $this->money($discount, $currency) . ' ويصبح السعر النهائي ' . $this->money($final, $currency) . '.',
        ];
    }

    private function percentage(array $input): array
    {
        $base = (float) ($input['base_value'] ?? 0);
        $percentage = (float) ($input['percentage_value'] ?? 0);
        $result = $base * ($percentage / 100);
        $onePercent = $base / 100;

        return [
            'rows' => [
                ['label' => 'النسبة من الرقم', 'value' => $this->format($result)],
                ['label' => 'القيمة الأصلية', 'value' => $this->format($base)],
                ['label' => 'قيمة 1%', 'value' => $this->format($onePercent)],
                ['label' => 'القيمة المتبقية بعد خصم النسبة', 'value' => $this->format($base - $result)],
            ],
            'summary' => $percentage . '% من ' . $this->format($base) . ' تساوي ' . $this->format($result) . '، بينما تمثل 1% من الرقم قيمة ' . $this->format($onePercent) . '.',
        ];
    }

    private function goldValue(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $grams = (float) ($input['grams'] ?? 0);
        $price = (float) ($input['price_per_gram'] ?? 0);
        $value = $grams * $price;
        $ounces = $grams / 31.1035;

        return [
            'rows' => [
                ['label' => 'قيمة الذهب', 'value' => $this->money($value, $currency)],
                ['label' => 'عدد الأوقيات التقريبي', 'value' => number_format($ounces, 2) . ' أوقية'],
                ['label' => 'قيمة الأوقية التقريبية', 'value' => $this->money($price * 31.1035, $currency)],
            ],
            'summary' => 'قيمة ' . $grams . ' جرام ذهب بسعر ' . $this->money($price, $currency) . ' للجرام هي ' . $this->money($value, $currency) . '، أي ما يعادل تقريبًا ' . number_format($ounces, 2) . ' أوقية.',
        ];
    }

    private function inflation(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $amount = (float) ($input['amount'] ?? 0);
        $rate = ((float) ($input['rate'] ?? 0)) / 100;
        $years = (float) ($input['years'] ?? 0);
        $futureCost = $amount * pow(1 + $rate, $years);
        $loss = $amount > 0 ? (1 - ($amount / max($futureCost, 0.01))) * 100 : 0;
        $monthlyInflation = $rate > 0 ? (pow(1 + $rate, 1 / 12) - 1) * 100 : 0;

        return [
            'rows' => [
                ['label' => 'القيمة بعد التضخم', 'value' => $this->money($futureCost, $currency)],
                ['label' => 'الزيادة المتوقعة', 'value' => $this->money($futureCost - $amount, $currency)],
                ['label' => 'معدل التضخم الشهري التقريبي', 'value' => $this->percent($monthlyInflation)],
                ['label' => 'تآكل القوة الشرائية', 'value' => $this->percent($loss)],
            ],
            'summary' => 'مبلغ ' . $this->money($amount, $currency) . ' مع تضخم سنوي ' . (($rate * 100)) . '% يصبح ' . $this->money($futureCost, $currency) . ' بعد ' . $years . ' سنة، مع تآكل تقريبي في القوة الشرائية قدره ' . $this->percent($loss) . '.',
        ];
    }

    private function salary(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $monthlySalary = (float) ($input['monthly_salary'] ?? 0);
        $deductions = ((float) ($input['deductions_rate'] ?? 0)) / 100;
        $allowances = (float) ($input['allowances'] ?? 0);
        $netMonthly = ($monthlySalary + $allowances) * (1 - $deductions);
        $grossMonthly = $monthlySalary + $allowances;
        $deductionValue = $grossMonthly - $netMonthly;
        $dailyNet = $netMonthly / 30;
        $hourlyNet = $dailyNet / 8;

        return [
            'rows' => [
                ['label' => 'صافي الراتب الشهري', 'value' => $this->money($netMonthly, $currency)],
                ['label' => 'صافي الراتب السنوي', 'value' => $this->money($netMonthly * 12, $currency)],
                ['label' => 'إجمالي الراتب قبل الاستقطاعات', 'value' => $this->money($grossMonthly, $currency)],
                ['label' => 'قيمة الاستقطاعات', 'value' => $this->money($deductionValue, $currency)],
                ['label' => 'صافي الراتب اليومي', 'value' => $this->money($dailyNet, $currency)],
                ['label' => 'صافي الراتب لكل ساعة', 'value' => $this->money($hourlyNet, $currency)],
            ],
            'summary' => 'صافي الراتب الشهري المتوقع هو ' . $this->money($netMonthly, $currency) . ' بعد استقطاعات تقدر بحوالي ' . $this->money($deductionValue, $currency) . '.',
        ];
    }

    private function age(array $input): array
    {
        if (empty($input['birth_date'])) {
            return [];
        }

        $birth = Carbon::parse($input['birth_date']);
        $now = now();
        $diff = $birth->diff($now);
        $nextBirthday = $birth->copy()->year($now->year);

        if ($nextBirthday->lessThanOrEqualTo($now)) {
            $nextBirthday->addYear();
        }

        $totalMonths = ($diff->y * 12) + $diff->m;
        $totalWeeks = $birth->diffInWeeks($now);
        $totalDays = $birth->diffInDays($now);
        $totalHours = $birth->diffInHours($now);
        $totalMinutes = $birth->diffInMinutes($now);
        $totalSeconds = $birth->diffInSeconds($now);
        $daysUntilBirthday = $now->diffInDays($nextBirthday);
        $nextBirthdayLabel = $daysUntilBirthday === 0
            ? 'اليوم'
            : 'بعد ' . $this->integer($daysUntilBirthday) . ' يوم';

        return [
            'rows' => [
                ['label' => 'العمر الكامل', 'value' => $this->integer($diff->y) . ' سنة و' . $this->integer($diff->m) . ' شهر و' . $this->integer($diff->d) . ' يوم'],
                ['label' => 'إجمالي الشهور', 'value' => $this->integer($totalMonths) . ' شهر'],
                ['label' => 'إجمالي الأسابيع', 'value' => $this->integer($totalWeeks) . ' أسبوع', 'key' => 'weeks'],
                ['label' => 'إجمالي الأيام', 'value' => $this->integer($totalDays) . ' يوم', 'key' => 'days'],
                ['label' => 'إجمالي الساعات', 'value' => $this->integer($totalHours) . ' ساعة', 'key' => 'hours'],
                ['label' => 'إجمالي الدقائق', 'value' => $this->integer($totalMinutes) . ' دقيقة', 'key' => 'minutes'],
                ['label' => 'إجمالي الثواني', 'value' => $this->integer($totalSeconds) . ' ثانية', 'key' => 'seconds'],
                ['label' => 'عيد الميلاد القادم', 'value' => $nextBirthday->translatedFormat('j F Y') . ' - ' . $nextBirthdayLabel],
            ],
            'summary' => 'العمر الحالي هو ' . $this->integer($diff->y) . ' سنة و' . $this->integer($diff->m) . ' شهر و' . $this->integer($diff->d) . ' يوم، ويتم تحديث الساعات والدقائق والثواني تلقائيًا أثناء فتح الصفحة.',
            'liveAge' => [
                'birthIso' => $birth->toIso8601String(),
            ],
        ];
    }

    private function daysBetween(array $input): array
    {
        if (empty($input['start_date']) || empty($input['end_date'])) {
            return [];
        }

        $start = Carbon::parse($input['start_date']);
        $end = Carbon::parse($input['end_date']);
        $days = $start->diffInDays($end, false);
        $absoluteDays = abs($days);
        $weeks = intdiv($absoluteDays, 7);
        $remainingDays = $absoluteDays % 7;
        $weekdays = $start->diffInWeekdays($end);

        return [
            'rows' => [
                ['label' => 'عدد الأيام', 'value' => (string) $absoluteDays],
                ['label' => 'المدة بالأسابيع والأيام', 'value' => $this->integer($weeks) . ' أسبوع و' . $this->integer($remainingDays) . ' يوم'],
                ['label' => 'أيام العمل التقريبية', 'value' => $this->integer($weekdays) . ' يوم'],
                ['label' => 'الاتجاه الزمني', 'value' => $days >= 0 ? 'التاريخ الثاني بعد الأول' : 'التاريخ الثاني قبل الأول'],
            ],
            'summary' => 'الفارق بين التاريخين هو ' . $absoluteDays . ' يوم، أي ما يعادل تقريبًا ' . $this->integer($weeks) . ' أسبوع و' . $this->integer($remainingDays) . ' يوم.',
        ];
    }

    private function timeDifference(array $input): array
    {
        if (empty($input['start_time']) || empty($input['end_time'])) {
            return [];
        }

        $start = Carbon::createFromFormat('H:i', $input['start_time']);
        $end = Carbon::createFromFormat('H:i', $input['end_time']);
        if ($end->lessThan($start)) {
            $end->addDay();
        }

        $minutes = $start->diffInMinutes($end);
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;
        $seconds = $minutes * 60;

        return [
            'rows' => [
                ['label' => 'الفارق بالساعات', 'value' => $hours . ' ساعة'],
                ['label' => 'الفارق بالدقائق', 'value' => (string) $minutes . ' دقيقة'],
                ['label' => 'الفارق بالثواني', 'value' => $this->integer($seconds) . ' ثانية'],
                ['label' => 'الصيغة المختصرة', 'value' => sprintf('%02d:%02d', $hours, $remainingMinutes)],
            ],
            'summary' => 'الفارق بين الوقتين هو ' . $hours . ' ساعة و' . $remainingMinutes . ' دقيقة، أي ' . $this->integer($seconds) . ' ثانية تقريبًا.',
        ];
    }

    private function bmi(array $input): array
    {
        $weight = (float) ($input['weight'] ?? 0);
        $heightCm = (float) ($input['height_cm'] ?? 0);
        $heightM = $heightCm / 100;
        $bmi = $heightM > 0 ? ($weight / ($heightM * $heightM)) : 0;
        $status = match (true) {
            $bmi < 18.5 => 'نحافة',
            $bmi < 25 => 'وزن طبيعي',
            $bmi < 30 => 'زيادة وزن',
            default => 'سمنة',
        };
        $healthyMin = 18.5 * ($heightM * $heightM);
        $healthyMax = 24.9 * ($heightM * $heightM);
        $weightNote = match (true) {
            $weight < $healthyMin => 'تحتاج زيادة تقريبية قدرها ' . number_format($healthyMin - $weight, 1) . ' كجم للوصول إلى الحد الأدنى الصحي',
            $weight > $healthyMax => 'تحتاج خفضًا تقريبيًا قدره ' . number_format($weight - $healthyMax, 1) . ' كجم للوصول إلى الحد الأعلى الصحي',
            default => 'وزنك الحالي داخل النطاق الصحي التقريبي',
        };

        return [
            'rows' => [
                ['label' => 'مؤشر كتلة الجسم', 'value' => number_format($bmi, 1)],
                ['label' => 'التصنيف', 'value' => $status],
                ['label' => 'نطاق الوزن الصحي', 'value' => number_format($healthyMin, 1) . ' - ' . number_format($healthyMax, 1) . ' كجم'],
                ['label' => 'ملاحظة الوزن الحالية', 'value' => $weightNote],
            ],
            'summary' => 'مؤشر كتلة الجسم لديك هو ' . number_format($bmi, 1) . ' ويصنف على أنه ' . $status . '. الوزن الصحي التقريبي لطولك يقع بين ' . number_format($healthyMin, 1) . ' و' . number_format($healthyMax, 1) . ' كجم.',
        ];
    }

    private function savingsGoal(array $input): array
    {
        $target = (float) ($input['target_amount'] ?? 0);
        $monthly = (float) ($input['monthly_save'] ?? 0);
        $annualReturn = ((float) ($input['annual_return'] ?? 0)) / 100;
        $months = 0;
        $balance = 0;
        $monthlyRate = $annualReturn / 12;

        while ($balance < $target && $months < 1200) {
            $balance = ($balance + $monthly) * (1 + $monthlyRate);
            $months++;
        }

        $savedCapital = $monthly * $months;
        $investmentGain = $balance - $savedCapital;
        $targetDate = now()->addMonths($months);

        return [
            'rows' => [
                ['label' => 'عدد الشهور المطلوبة', 'value' => (string) $months],
                ['label' => 'عدد السنوات التقريبية', 'value' => number_format($months / 12, 1)],
                ['label' => 'إجمالي المبالغ المدخرة', 'value' => $this->format($savedCapital)],
                ['label' => 'العائد المتراكم', 'value' => $this->format($investmentGain)],
                ['label' => 'تاريخ الوصول المتوقع', 'value' => $targetDate->translatedFormat('j F Y')],
            ],
            'summary' => 'تحتاج تقريبًا إلى ' . $months . ' شهر للوصول إلى هدفك الادخاري، مع تاريخ متوقع للوصول في ' . $targetDate->translatedFormat('j F Y') . '.',
        ];
    }

    private function breakEven(array $input): array
    {
        $currency = $this->resolveCurrency($input);
        $fixed = (float) ($input['fixed_costs'] ?? 0);
        $price = (float) ($input['unit_price'] ?? 0);
        $variable = (float) ($input['variable_cost'] ?? 0);
        $contribution = $price - $variable;
        $units = $contribution > 0 ? ceil($fixed / $contribution) : 0;
        $marginRatio = $price > 0 ? ($contribution / $price) * 100 : 0;

        return [
            'rows' => [
                ['label' => 'نقطة التعادل بالوحدات', 'value' => (string) $units],
                ['label' => 'نقطة التعادل بالمبيعات', 'value' => $this->money($units * $price, $currency)],
                ['label' => 'هامش المساهمة للوحدة', 'value' => $this->money($contribution, $currency)],
                ['label' => 'نسبة هامش المساهمة', 'value' => $this->percent($marginRatio)],
            ],
            'summary' => 'تصل إلى نقطة التعادل بعد بيع ' . $units . ' وحدة تقريبًا، مع هامش مساهمة للوحدة يساوي ' . $this->money($contribution, $currency) . '.',
        ];
    }

    private function inlineUnitConversion(Tool $tool, array $input): array
    {
        $amount = (float) ($input['amount'] ?? 1);
        $ratio = (float) data_get($tool->settings, 'ratio', 1);
        $from = (string) data_get($tool->settings, 'from', 'الوحدة');
        $to = (string) data_get($tool->settings, 'to', 'الوحدة');
        $result = $amount * $ratio;
        $reverseRatio = $ratio > 0 ? 1 / $ratio : 0;

        return [
            'rows' => [
                ['label' => 'المقدار المدخل', 'value' => $this->format($amount) . ' ' . $from],
                ['label' => 'الناتج', 'value' => $this->format($result) . ' ' . $to],
                ['label' => 'معدل التحويل', 'value' => '1 ' . $from . ' = ' . $this->format($ratio) . ' ' . $to],
                ['label' => 'معدل التحويل العكسي', 'value' => '1 ' . $to . ' = ' . $this->format($reverseRatio) . ' ' . $from],
            ],
            'summary' => $this->format($amount) . ' ' . $from . ' تساوي ' . $this->format($result) . ' ' . $to . ' وفق معدل تحويل تقريبي ثابت داخل الأداة.',
        ];
    }

    public static function currencyOptions(): array
    {
        $options = [];

        foreach (self::CURRENCIES as $code => $currency) {
            $options[] = [
                'value' => $code,
                'label' => $currency['label'] . ' (' . $currency['symbol'] . ')',
            ];
        }

        return $options;
    }

    private function resolveCurrency(array $input): array
    {
        $code = strtoupper((string) ($input['currency'] ?? 'EGP'));

        return self::CURRENCIES[$code] ?? self::CURRENCIES['EGP'];
    }

    private function money(float $value, ?array $currency = null): string
    {
        $currency ??= self::CURRENCIES['EGP'];

        return $this->format($value) . ' ' . $currency['symbol'];
    }

    private function percent(float $value): string
    {
        return number_format($value, 2) . '%';
    }

    private function format(float $value): string
    {
        return number_format($value, 2, '.', ',');
    }

    private function integer(int|float $value): string
    {
        return number_format((float) $value, 0, '.', ',');
    }
}
