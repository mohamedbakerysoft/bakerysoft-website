<?php

namespace App\Services;

use App\Models\Conversion;
use App\Models\Tool;
use Carbon\Carbon;

class ToolCalculator
{
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

    public function convertPage(Conversion $conversion, float $amount): array
    {
        $result = $amount * $conversion->ratio;

        return [
            'rows' => [
                ['label' => 'القيمة المدخلة', 'value' => $this->format($amount)],
                ['label' => 'سعر التحويل', 'value' => '1 ' . $conversion->from_unit_ar . ' = ' . $this->format($conversion->ratio) . ' ' . $conversion->to_unit_ar],
                ['label' => 'الناتج', 'value' => $this->format($result) . ' ' . $conversion->to_unit_ar],
            ],
            'summary' => 'كل ' . $this->format($amount) . ' ' . $conversion->from_unit_ar . ' تساوي ' . $this->format($result) . ' ' . $conversion->to_unit_ar,
        ];
    }

    private function compoundInterest(array $input): array
    {
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

        return [
            'rows' => [
                ['label' => 'القيمة المستقبلية', 'value' => $this->money($balance)],
                ['label' => 'إجمالي المساهمات', 'value' => $this->money($contributed)],
                ['label' => 'الأرباح المتوقعة', 'value' => $this->money($profit)],
            ],
            'summary' => 'إذا استثمرت ' . $this->money($principal) . ' بمعدل ' . (($rate * 100)) . '% لمدة ' . $years . ' سنة فالقيمة المتوقعة هي ' . $this->money($balance),
            'chart' => $points,
        ];
    }

    private function loan(array $input): array
    {
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

        return [
            'rows' => [
                ['label' => 'القسط الشهري', 'value' => $this->money($payment)],
                ['label' => 'إجمالي السداد', 'value' => $this->money($total)],
                ['label' => 'إجمالي الفوائد', 'value' => $this->money($interest)],
            ],
            'summary' => 'قسط قرض بقيمة ' . $this->money($amount) . ' لمدة ' . $years . ' سنة يساوي تقريبًا ' . $this->money($payment) . ' شهريًا.',
        ];
    }

    private function investmentReturn(array $input): array
    {
        $initial = (float) ($input['initial_amount'] ?? 0);
        $final = (float) ($input['final_amount'] ?? 0);
        $months = max((float) ($input['period_months'] ?? 1), 1);
        $profit = $final - $initial;
        $roi = $initial > 0 ? ($profit / $initial) * 100 : 0;
        $annualized = $initial > 0 ? ((pow(max($final, 0.01) / max($initial, 0.01), 12 / $months) - 1) * 100) : 0;

        return [
            'rows' => [
                ['label' => 'الربح الصافي', 'value' => $this->money($profit)],
                ['label' => 'نسبة العائد', 'value' => $this->percent($roi)],
                ['label' => 'العائد السنوي التقريبي', 'value' => $this->percent($annualized)],
            ],
            'summary' => 'ارتفع الاستثمار من ' . $this->money($initial) . ' إلى ' . $this->money($final) . ' بعائد إجمالي ' . $this->percent($roi) . '.',
        ];
    }

    private function tradeProfit(array $input): array
    {
        $quantity = (float) ($input['quantity'] ?? 0);
        $buy = (float) ($input['buy_price'] ?? 0);
        $sell = (float) ($input['sell_price'] ?? 0);
        $fees = (float) ($input['fees'] ?? 0);
        $cost = ($quantity * $buy) + $fees;
        $revenue = ($quantity * $sell) - $fees;
        $profit = $revenue - $cost;
        $margin = $cost > 0 ? ($profit / $cost) * 100 : 0;

        return [
            'rows' => [
                ['label' => 'تكلفة الشراء', 'value' => $this->money($cost)],
                ['label' => 'قيمة البيع', 'value' => $this->money($revenue)],
                ['label' => 'صافي الربح', 'value' => $this->money($profit)],
                ['label' => 'نسبة الربح', 'value' => $this->percent($margin)],
            ],
            'summary' => 'صافي الربح من الصفقة يساوي ' . $this->money($profit) . ' بنسبة ' . $this->percent($margin) . '.',
        ];
    }

    private function profitMargin(array $input): array
    {
        $revenue = (float) ($input['revenue'] ?? 0);
        $cost = (float) ($input['cost'] ?? 0);
        $profit = $revenue - $cost;
        $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;

        return [
            'rows' => [
                ['label' => 'الربح', 'value' => $this->money($profit)],
                ['label' => 'هامش الربح', 'value' => $this->percent($margin)],
            ],
            'summary' => 'عند إيرادات ' . $this->money($revenue) . ' وتكلفة ' . $this->money($cost) . ' يصبح هامش الربح ' . $this->percent($margin) . '.',
        ];
    }

    private function vat(array $input): array
    {
        $amount = (float) ($input['amount'] ?? 0);
        $rate = (float) ($input['rate'] ?? 0);
        $vat = $amount * ($rate / 100);
        $total = $amount + $vat;

        return [
            'rows' => [
                ['label' => 'قيمة الضريبة', 'value' => $this->money($vat)],
                ['label' => 'الإجمالي بعد الضريبة', 'value' => $this->money($total)],
            ],
            'summary' => 'ضريبة القيمة المضافة على ' . $this->money($amount) . ' بنسبة ' . $rate . '% تساوي ' . $this->money($vat) . '.',
        ];
    }

    private function discount(array $input): array
    {
        $price = (float) ($input['original_price'] ?? 0);
        $rate = (float) ($input['discount_rate'] ?? 0);
        $discount = $price * ($rate / 100);
        $final = $price - $discount;

        return [
            'rows' => [
                ['label' => 'قيمة الخصم', 'value' => $this->money($discount)],
                ['label' => 'السعر بعد الخصم', 'value' => $this->money($final)],
            ],
            'summary' => 'بعد خصم ' . $rate . '% يصبح السعر النهائي ' . $this->money($final) . '.',
        ];
    }

    private function percentage(array $input): array
    {
        $base = (float) ($input['base_value'] ?? 0);
        $percentage = (float) ($input['percentage_value'] ?? 0);
        $result = $base * ($percentage / 100);

        return [
            'rows' => [
                ['label' => 'النسبة من الرقم', 'value' => $this->format($result)],
                ['label' => 'القيمة الأصلية', 'value' => $this->format($base)],
            ],
            'summary' => $percentage . '% من ' . $this->format($base) . ' تساوي ' . $this->format($result) . '.',
        ];
    }

    private function goldValue(array $input): array
    {
        $grams = (float) ($input['grams'] ?? 0);
        $price = (float) ($input['price_per_gram'] ?? 0);
        $value = $grams * $price;

        return [
            'rows' => [
                ['label' => 'قيمة الذهب', 'value' => $this->money($value)],
            ],
            'summary' => 'قيمة ' . $grams . ' جرام ذهب بسعر ' . $this->money($price) . ' للجرام هي ' . $this->money($value) . '.',
        ];
    }

    private function inflation(array $input): array
    {
        $amount = (float) ($input['amount'] ?? 0);
        $rate = ((float) ($input['rate'] ?? 0)) / 100;
        $years = (float) ($input['years'] ?? 0);
        $futureCost = $amount * pow(1 + $rate, $years);

        return [
            'rows' => [
                ['label' => 'القيمة بعد التضخم', 'value' => $this->money($futureCost)],
                ['label' => 'الزيادة المتوقعة', 'value' => $this->money($futureCost - $amount)],
            ],
            'summary' => 'مبلغ ' . $this->money($amount) . ' مع تضخم سنوي ' . (($rate * 100)) . '% يصبح ' . $this->money($futureCost) . ' بعد ' . $years . ' سنة.',
        ];
    }

    private function salary(array $input): array
    {
        $monthlySalary = (float) ($input['monthly_salary'] ?? 0);
        $deductions = ((float) ($input['deductions_rate'] ?? 0)) / 100;
        $allowances = (float) ($input['allowances'] ?? 0);
        $netMonthly = ($monthlySalary + $allowances) * (1 - $deductions);

        return [
            'rows' => [
                ['label' => 'صافي الراتب الشهري', 'value' => $this->money($netMonthly)],
                ['label' => 'صافي الراتب السنوي', 'value' => $this->money($netMonthly * 12)],
            ],
            'summary' => 'صافي الراتب الشهري المتوقع هو ' . $this->money($netMonthly) . '.',
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

        return [
            'rows' => [
                ['label' => 'العمر بالسنوات', 'value' => (string) $diff->y],
                ['label' => 'العمر بالشهور', 'value' => (string) ($diff->y * 12 + $diff->m)],
                ['label' => 'العمر بالأيام', 'value' => (string) $birth->diffInDays($now)],
            ],
            'summary' => 'العمر الحالي هو ' . $diff->y . ' سنة و' . $diff->m . ' شهر.',
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

        return [
            'rows' => [
                ['label' => 'عدد الأيام', 'value' => (string) abs($days)],
            ],
            'summary' => 'الفارق بين التاريخين هو ' . abs($days) . ' يوم.',
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

        return [
            'rows' => [
                ['label' => 'الفارق بالساعات', 'value' => floor($minutes / 60) . ' ساعة'],
                ['label' => 'الفارق بالدقائق', 'value' => (string) $minutes . ' دقيقة'],
            ],
            'summary' => 'الفارق بين الوقتين هو ' . floor($minutes / 60) . ' ساعة و' . ($minutes % 60) . ' دقيقة.',
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

        return [
            'rows' => [
                ['label' => 'مؤشر كتلة الجسم', 'value' => number_format($bmi, 1)],
                ['label' => 'التصنيف', 'value' => $status],
            ],
            'summary' => 'مؤشر كتلة الجسم لديك هو ' . number_format($bmi, 1) . ' ويصنف على أنه ' . $status . '.',
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

        return [
            'rows' => [
                ['label' => 'عدد الشهور المطلوبة', 'value' => (string) $months],
                ['label' => 'عدد السنوات التقريبية', 'value' => number_format($months / 12, 1)],
            ],
            'summary' => 'تحتاج تقريبًا إلى ' . $months . ' شهر للوصول إلى هدفك الادخاري.',
        ];
    }

    private function breakEven(array $input): array
    {
        $fixed = (float) ($input['fixed_costs'] ?? 0);
        $price = (float) ($input['unit_price'] ?? 0);
        $variable = (float) ($input['variable_cost'] ?? 0);
        $contribution = $price - $variable;
        $units = $contribution > 0 ? ceil($fixed / $contribution) : 0;

        return [
            'rows' => [
                ['label' => 'نقطة التعادل بالوحدات', 'value' => (string) $units],
                ['label' => 'نقطة التعادل بالمبيعات', 'value' => $this->money($units * $price)],
            ],
            'summary' => 'تصل إلى نقطة التعادل بعد بيع ' . $units . ' وحدة تقريبًا.',
        ];
    }

    private function inlineUnitConversion(Tool $tool, array $input): array
    {
        $amount = (float) ($input['amount'] ?? 1);
        $ratio = (float) data_get($tool->settings, 'ratio', 1);
        $from = (string) data_get($tool->settings, 'from', 'الوحدة');
        $to = (string) data_get($tool->settings, 'to', 'الوحدة');
        $result = $amount * $ratio;

        return [
            'rows' => [
                ['label' => 'المقدار المدخل', 'value' => $this->format($amount) . ' ' . $from],
                ['label' => 'الناتج', 'value' => $this->format($result) . ' ' . $to],
            ],
            'summary' => $this->format($amount) . ' ' . $from . ' تساوي ' . $this->format($result) . ' ' . $to . '.',
        ];
    }

    private function money(float $value): string
    {
        return $this->format($value) . ' ر.س';
    }

    private function percent(float $value): string
    {
        return number_format($value, 2) . '%';
    }

    private function format(float $value): string
    {
        return number_format($value, 2, '.', ',');
    }
}
