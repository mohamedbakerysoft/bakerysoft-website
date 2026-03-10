<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Conversion;
use App\Models\Tool;
use App\Services\ToolCalculator;
use Illuminate\Http\Request;

class ConversionController extends Controller
{
    public function __invoke(Request $request, string $from, string $to, ToolCalculator $calculator)
    {
        $conversion = Conversion::where('from_slug_ar', $from)
            ->where('to_slug_ar', $to)
            ->firstOrFail();

        $amount = (float) $request->query('amount', 1);
        $result = $calculator->convertPage($conversion, $amount);
        $category = Category::where('slug_ar', 'المحولات')->first();
        $relatedTools = Tool::with('category')
            ->whereBelongsTo($category)
            ->take(6)
            ->get();
        $nearbyConversions = Conversion::where('group_key', $conversion->group_key)
            ->where('from_slug_ar', $conversion->from_slug_ar)
            ->whereKeyNot($conversion->id)
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
                'intro' => 'أدخل القيمة التي تريد تحويلها من ' . $conversion->from_unit_ar . ' إلى ' . $conversion->to_unit_ar . ' وستظهر لك نتيجة تقريبية مرجعية مناسبة للمقارنة السريعة، وليست بديلاً عن سعر السوق اللحظي أو سعر التنفيذ البنكي.',
                'usage' => [
                    'أدخل المبلغ الذي تريد مراجعته ثم اضغط على زر التحويل للحصول على قيمة تقريبية فورية.',
                    'هذه الصفحة مناسبة للتقدير السريع أثناء المقارنة أو الدراسة أو التخطيط المالي الأولي، لكنها لا تمثل تسعيرًا مباشرًا من بنك أو وسيط.',
                    'إذا كنت تحتاج سعرًا نهائيًا للتنفيذ، فاعتمد على مزود الأسعار أو الجهة المالية التي ستتم عبرها العملية.',
                ],
                'faq' => [
                    [
                        'question' => 'هل هذا السعر لحظي؟',
                        'answer' => 'لا. هذه الصفحة تعرض سعرًا مرجعيًا تقريبيًا لأغراض الحساب والمقارنة السريعة، وقد يختلف عن السعر الفعلي وقت التنفيذ.',
                    ],
                    [
                        'question' => 'متى أستخدم هذه الصفحة؟',
                        'answer' => 'استخدمها عندما تريد تقديرًا سريعًا للمبلغ أو مقارنة أولية بين العملات قبل الرجوع إلى مصدر التسعير المباشر.',
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
