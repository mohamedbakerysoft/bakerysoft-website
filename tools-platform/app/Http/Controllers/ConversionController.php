<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Conversion;
use App\Models\Tool;
use App\Services\LiveCurrencyRateService;
use App\Services\ToolCalculator;
use Illuminate\Http\Request;

class ConversionController extends Controller
{
    public function __invoke(Request $request, string $from, string $to, ToolCalculator $calculator, LiveCurrencyRateService $liveCurrencyRateService)
    {
        $conversion = Conversion::where('from_slug_ar', $from)
            ->where('to_slug_ar', $to)
            ->firstOrFail();

        $amount = (float) $request->query('amount', 1);
        $liveRate = null;

        if ($conversion->group_key === 'currency') {
            $liveRate = $liveCurrencyRateService->getRateForArabicUnits($conversion->from_unit_ar, $conversion->to_unit_ar);
        }

        $result = $calculator->convertPage($conversion, $amount, $liveRate);
        $category = Category::where('slug_ar', 'المحولات')->first();
        $relatedTools = Tool::with('category')
            ->whereBelongsTo($category)
            ->popular()
            ->take(6)
            ->get();
        $nearbyConversions = Conversion::where('group_key', $conversion->group_key)
            ->where('from_slug_ar', $conversion->from_slug_ar)
            ->whereKeyNot($conversion->id)
            ->orderBy('to_unit_ar')
            ->take(8)
            ->get();
        $pageCopy = $this->pageCopy($conversion);

        return view('conversion', [
            'conversion' => $conversion,
            'amount' => $amount,
            'result' => $result,
            'relatedTools' => $relatedTools,
            'nearbyConversions' => $nearbyConversions,
            'metaTitle' => 'تحويل ' . $conversion->from_unit_ar . ' إلى ' . $conversion->to_unit_ar . ' | Calclyo',
            'metaDescription' => 'حوّل بسهولة من ' . $conversion->from_unit_ar . ' إلى ' . $conversion->to_unit_ar . ' مع شرح وأمثلة وأسئلة شائعة وروابط داخلية.',
            'canonicalUrl' => route('conversion.show', ['from' => $conversion->from_slug_ar, 'to' => $conversion->to_slug_ar]),
            'metaRobots' => $request->query() ? 'noindex,follow' : 'index,follow',
            'pageCopy' => $pageCopy,
        ]);
    }

    private function pageCopy(Conversion $conversion): array
    {
        if ($conversion->group_key === 'currency') {
            return [
                'intro' => 'أدخل القيمة التي تريد تحويلها من ' . $conversion->from_unit_ar . ' إلى ' . $conversion->to_unit_ar . ' وستظهر لك نتيجة مرجعية محدثة عند الطلب من مصدر مجاني خفيف، مع fallback احتياطي إذا تعذر الوصول إلى السعر الحي.',
                'usage' => [
                    'أدخل المبلغ الذي تريد مراجعته ثم اضغط على زر التحويل للحصول على قيمة مرجعية محدثة عند الطلب.',
                    'نستخدم سعرًا حيًا مرجعيًا مع كاش قصير لتحسين السرعة وتقليل عدد الطلبات الخارجية، ثم نرجع إلى السعر الاحتياطي المخزن إذا تعذر الوصول إلى المزود.',
                    'إذا كنت تحتاج سعرًا نهائيًا للتنفيذ، فاعتمد على مزود الأسعار أو الجهة المالية التي ستتم عبرها العملية.',
                ],
                'faq' => [
                    [
                        'question' => 'هل هذا السعر لحظي؟',
                        'answer' => 'هو سعر حي مرجعي يتم طلبه عند الحاجة مع كاش قصير لتسريع الأداء. لذلك قد يختلف عن سعر التنفيذ النهائي لدى البنك أو الوسيط.',
                    ],
                    [
                        'question' => 'متى أستخدم هذه الصفحة؟',
                        'answer' => 'استخدمها عندما تريد تقديرًا سريعًا للمبلغ أو مقارنة أولية بين العملات قبل الرجوع إلى جهة التنفيذ الفعلية.',
                    ],
                ],
            ];
        }

        return [
            'intro' => 'أدخل القيمة التي تريد تحويلها من ' . $conversion->from_unit_ar . ' إلى ' . $conversion->to_unit_ar . ' وستظهر لك النتيجة فورًا بناءً على معامل تحويل ثابت مناسب للوحدات القياسية.',
            'usage' => [
                'أدخل القيمة التي تريد تحويلها ثم اضغط على زر التحويل.',
                'ستحصل على الناتج مباشرة مع معامل التحويل المستخدم داخل الصفحة حتى تتمكن من المراجعة أو المقارنة بسرعة.',
                'يمكنك الانتقال إلى تحويلات أخرى من نفس الوحدة المصدر عبر الروابط ذات الصلة أدناه.',
            ],
            'faq' => [
                [
                    'question' => 'هل هذا التحويل ثابت؟',
                    'answer' => 'نعم، في الوحدات القياسية يكون معامل التحويل ثابتًا رياضيًا، لذلك تكون النتيجة مناسبة للاستخدام اليومي والتعليم والمقارنة.',
                ],
                [
                    'question' => 'كيف أستخدم هذه الصفحة في المقارنة؟',
                    'answer' => 'بدّل القيمة أكثر من مرة، وجرّب الروابط المجاورة للوصول إلى وجهات تحويل مختلفة من نفس الوحدة.',
                ],
            ],
        ];
    }
}
