<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Conversion;
use App\Models\Tool;
use App\Support\ArabicSlug;
use Illuminate\Database\Seeder;

class ToolsPlatformSeeder extends Seeder
{
    public function run(): void
    {
        Conversion::query()->delete();
        Tool::query()->delete();
        Category::query()->delete();

        $categories = collect([
            [
                'name_ar' => 'أدوات الاستثمار',
                'slug_ar' => 'ادوات-الاستثمار',
                'headline' => 'حاسبات واستراتيجيات الاستثمار والنمو طويل الأجل',
                'description' => 'اكتشف أدوات الاستثمار العربية لحساب العائد والفائدة المركبة والأرباح المتوقعة وإدارة المحافظ.',
            ],
            [
                'name_ar' => 'أدوات المال',
                'slug_ar' => 'ادوات-المال',
                'headline' => 'حسابات التمويل والميزانية والربحية',
                'description' => 'مجموعة واسعة من حاسبات القروض والضرائب والخصومات والربح والسيولة النقدية.',
            ],
            [
                'name_ar' => 'المحولات',
                'slug_ar' => 'المحولات',
                'headline' => 'محولات العملات والأطوال والأوزان والبيانات',
                'description' => 'آلاف صفحات التحويل البرمجية بين العملات والوحدات مع شروحات وأسئلة شائعة وروابط ذات صلة.',
            ],
            [
                'name_ar' => 'الحاسبات اليومية',
                'slug_ar' => 'الحاسبات-اليومية',
                'headline' => 'أدوات يومية للحياة والعمل والصحة',
                'description' => 'حساب العمر وفرق التواريخ والوقت ومؤشر كتلة الجسم والراتب والادخار والمهام اليومية.',
            ],
        ])->mapWithKeys(fn (array $category) => [
            $category['slug_ar'] => Category::query()->create($category),
        ]);

        $toolRows = array_merge(
            $this->investmentTools($categories['ادوات-الاستثمار']->id),
            $this->financeTools($categories['ادوات-المال']->id),
            $this->dailyTools($categories['الحاسبات-اليومية']->id),
            $this->converterTools($categories['المحولات']->id),
        );

        foreach ($toolRows as $toolRow) {
            Tool::query()->create($toolRow);
        }

        $this->seedConversions();
    }

    private function investmentTools(int $categoryId): array
    {
        $assets = ['الأسهم', 'العملات الرقمية', 'الذهب', 'الصناديق', 'العقارات', 'السندات', 'المؤشرات', 'المحافظ', 'الصكوك', 'المشاريع'];
        $rows = [];

        $rows[] = $this->tool($categoryId, 'حاسبة الفائدة المركبة', 'compound_interest', $this->compoundSchema(), ['featured' => true]);
        $rows[] = $this->tool($categoryId, 'حاسبة العائد السنوي المركب', 'investment_return', $this->investmentSchema(), ['featured' => true]);
        $rows[] = $this->tool($categoryId, 'حاسبة هدف الادخار الاستثماري', 'savings_goal', $this->savingsSchema(), ['featured' => true]);
        $rows[] = $this->tool($categoryId, 'حاسبة ربح الأسهم', 'stock_profit', $this->tradeSchema(), ['featured' => true]);
        $rows[] = $this->tool($categoryId, 'حاسبة ربح العملات الرقمية', 'crypto_profit', $this->tradeSchema(), ['featured' => true]);

        foreach ($assets as $asset) {
            $rows[] = $this->tool($categoryId, 'حاسبة عائد ' . $asset, 'investment_return', $this->investmentSchema());
            $rows[] = $this->tool($categoryId, 'حاسبة نمو ' . $asset, 'compound_interest', $this->compoundSchema());
            $rows[] = $this->tool($categoryId, 'حاسبة خطة ادخار ' . $asset, 'savings_goal', $this->savingsSchema());
            $rows[] = $this->tool($categoryId, 'حاسبة استرداد رأس مال ' . $asset, 'investment_return', $this->investmentSchema());
            $rows[] = $this->tool($categoryId, 'حاسبة نسبة الربح في ' . $asset, 'investment_return', $this->investmentSchema());
        }

        return array_slice($rows, 0, 50);
    }

    private function financeTools(int $categoryId): array
    {
        $loanTypes = ['الشخصي', 'العقاري', 'السيارة', 'التعليمي', 'التجاري', 'التشغيلي', 'قصير الأجل', 'الاستثماري', 'الزراعي', 'الطبي'];
        $rows = [
            $this->tool($categoryId, 'حاسبة القرض', 'loan', $this->loanSchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة ضريبة القيمة المضافة', 'vat', $this->vatSchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة الخصم', 'discount', $this->discountSchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة النسبة المئوية', 'percentage', $this->percentageSchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة هامش الربح', 'profit_margin', $this->marginSchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة قيمة الذهب', 'gold_value', $this->goldSchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة التضخم', 'inflation', $this->inflationSchema()),
            $this->tool($categoryId, 'حاسبة نقطة التعادل', 'break_even', $this->breakEvenSchema()),
        ];

        foreach ($loanTypes as $loanType) {
            $rows[] = $this->tool($categoryId, 'حاسبة القرض ' . $loanType, 'loan', $this->loanSchema());
            $rows[] = $this->tool($categoryId, 'حاسبة تكلفة القرض ' . $loanType, 'loan', $this->loanSchema());
            $rows[] = $this->tool($categoryId, 'حاسبة القسط الشهري للقرض ' . $loanType, 'loan', $this->loanSchema());
        }

        foreach (['المتجر الإلكتروني', 'المطعم', 'المخبز', 'الشركة', 'المشروع الصغير', 'الخدمات', 'الوكالة', 'المصنع', 'التطبيق', 'الاشتراك'] as $business) {
            $rows[] = $this->tool($categoryId, 'حاسبة هامش ربح ' . $business, 'profit_margin', $this->marginSchema());
            $rows[] = $this->tool($categoryId, 'حاسبة ضريبة ' . $business, 'vat', $this->vatSchema());
        }

        return array_slice($rows, 0, 50);
    }

    private function dailyTools(int $categoryId): array
    {
        $rows = [
            $this->tool($categoryId, 'حاسبة العمر', 'age', $this->ageSchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة الأيام بين تاريخين', 'days_between', $this->daysSchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة فرق الوقت', 'time_difference', $this->timeSchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة مؤشر كتلة الجسم', 'bmi', $this->bmiSchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة الراتب', 'salary', $this->salarySchema(), ['featured' => true]),
            $this->tool($categoryId, 'حاسبة خطة الادخار', 'savings_goal', $this->savingsSchema()),
        ];

        foreach (['العمل', 'الموظف', 'المستقل', 'المعلم', 'المهندس', 'المحاسب', 'المدير', 'المطور', 'المبيعات', 'الدوام الجزئي', 'الإدارة', 'الاستشارات'] as $role) {
            $rows[] = $this->tool($categoryId, 'حاسبة راتب ' . $role, 'salary', $this->salarySchema());
        }

        foreach (['الطفل', 'الطالب', 'الموظف', 'المتقاعد', 'المشروع', 'السفر', 'الدراسة', 'التمرين', 'الروتين', 'الإجازة'] as $context) {
            $rows[] = $this->tool($categoryId, 'حاسبة فرق الأيام لـ ' . $context, 'days_between', $this->daysSchema());
            $rows[] = $this->tool($categoryId, 'حاسبة فرق الوقت لـ ' . $context, 'time_difference', $this->timeSchema());
        }

        foreach (['الرجال', 'النساء', 'المراهقين', 'الرياضيين', 'المبتدئين', 'الوزن المثالي', 'الحمية', 'اللياقة', 'الصحة', 'النشاط', 'الأطفال', 'الحوامل'] as $health) {
            $rows[] = $this->tool($categoryId, 'حاسبة BMI لـ ' . $health, 'bmi', $this->bmiSchema());
        }

        return array_slice($rows, 0, 50);
    }

    private function converterTools(int $categoryId): array
    {
        $pairs = [
            ['دولار أمريكي', 'جنيه مصري', 50.65],
            ['ريال سعودي', 'جنيه مصري', 13.52],
            ['دولار أمريكي', 'ريال سعودي', 3.75],
            ['يورو', 'دولار أمريكي', 1.09],
            ['جرام', 'أوقية', 0.035274],
            ['كيلومتر', 'ميل', 0.621371],
            ['متر', 'قدم', 3.28084],
            ['سم', 'إنش', 0.393701],
            ['لتر', 'جالون', 0.264172],
            ['ميجابايت', 'جيجابايت', 0.0009765625],
            ['كيلوجرام', 'باوند', 2.20462],
            ['هكتار', 'متر مربع', 10000],
            ['كيلووات', 'حصان', 1.34102],
            ['نيوتن', 'كيلوجرام قوة', 0.101972],
            ['كيلومتر في الساعة', 'متر في الثانية', 0.277778],
            ['بايت', 'كيلوبايت', 0.0009765625],
            ['جنيه مصري', 'دولار أمريكي', 0.01974],
            ['ريال سعودي', 'دولار أمريكي', 0.266667],
            ['باوند', 'كيلوجرام', 0.453592],
            ['قدم', 'متر', 0.3048],
            ['لتر', 'ملليلتر', 1000],
            ['جيجابايت', 'ميجابايت', 1024],
            ['هكتار', 'فدان', 2.381],
            ['متر مربع', 'قدم مربع', 10.7639],
            ['درهم إماراتي', 'ريال سعودي', 1.021],
        ];

        $rows = [];
        foreach ($pairs as [$from, $to, $ratio]) {
            $rows[] = $this->tool(
                $categoryId,
                'محول ' . $from . ' إلى ' . $to,
                'unit_conversion_lookup',
                [
                    ['name' => 'amount', 'label' => 'القيمة', 'type' => 'number', 'step' => 'any', 'default' => 1],
                ],
                [
                    'settings' => ['from' => $from, 'to' => $to, 'ratio' => $ratio],
                ]
            );
            $rows[] = $this->tool(
                $categoryId,
                'حاسبة تحويل ' . $from . ' إلى ' . $to,
                'unit_conversion_lookup',
                [
                    ['name' => 'amount', 'label' => 'القيمة', 'type' => 'number', 'step' => 'any', 'default' => 1],
                ],
                [
                    'settings' => ['from' => $from, 'to' => $to, 'ratio' => $ratio],
                ]
            );
        }

        return array_slice($rows, 0, 50);
    }

    private function seedConversions(): void
    {
        $currencies = [
            'دولار أمريكي' => 1, 'يورو' => 0.92, 'جنيه إسترليني' => 0.78, 'ريال سعودي' => 3.75, 'درهم إماراتي' => 3.67,
            'دينار كويتي' => 0.31, 'ريال قطري' => 3.64, 'دينار أردني' => 0.71, 'جنيه مصري' => 50.65, 'درهم مغربي' => 10.02,
            'دينار بحريني' => 0.38, 'ليرة تركية' => 32.15, 'فرنك سويسري' => 0.88, 'ين ياباني' => 149.6, 'يوان صيني' => 7.18,
            'روبية هندية' => 82.7, 'روبية باكستانية' => 278.5, 'دينار عراقي' => 1310, 'ليرة لبنانية' => 89500, 'روبل روسي' => 90.1,
            'كرونة سويدية' => 10.4, 'كرونة نرويجية' => 10.6, 'كرونة دنماركية' => 6.86, 'دولار كندي' => 1.35, 'دولار أسترالي' => 1.52,
            'دولار نيوزيلندي' => 1.64, 'دولار سنغافوري' => 1.34, 'وون كوري جنوبي' => 1330, 'بيزو مكسيكي' => 16.9, 'ريال برازيلي' => 4.98,
            'راند جنوب أفريقي' => 18.6, 'بيزو أرجنتيني' => 850, 'بيزو تشيلي' => 950, 'بيزو كولومبي' => 3900, 'سول بيروفي' => 3.73,
            'بوليفار فنزويلي' => 36.4, 'كرونة تشيكية' => 23.3, 'زلوتي بولندي' => 3.95, 'فورنت مجري' => 360, 'ليو روماني' => 4.58,
            'ليف بلغاري' => 1.8, 'هريفنيا أوكرانية' => 39.5, 'دينار جزائري' => 134.2, 'دينار تونسي' => 3.11, 'أوقية موريتانية' => 39.7,
            'شلن كيني' => 128.4, 'فرنك إفريقي' => 604, 'بير إثيوبي' => 57.2, 'شلن أوغندي' => 3850, 'فرنك رواندي' => 1289,
            'دينار صربي' => 107.8, 'لار جورجي' => 2.69, 'درام أرميني' => 403, 'تينغي كازاخستاني' => 447, 'سوم قيرغيزستاني' => 89.3,
            'سوم أوزبكي' => 12600, 'دونغ فيتنامي' => 24600, 'بات تايلندي' => 35.2, 'رينغيت ماليزي' => 4.7, 'روبية إندونيسية' => 15600,
        ];

        $length = [
            'ملليمتر' => 0.001, 'سنتيمتر' => 0.01, 'ديسيمتر' => 0.1, 'متر' => 1, 'ديكامتر' => 10, 'هيكتومتر' => 100,
            'كيلومتر' => 1000, 'إنش' => 0.0254, 'قدم' => 0.3048, 'ياردة' => 0.9144, 'ميل' => 1609.344, 'ميل بحري' => 1852,
            'ميكرومتر' => 0.000001, 'نانومتر' => 0.000000001, 'فرسخ' => 4828.032, 'ذراع' => 0.58, 'شبر' => 0.22, 'قصبة' => 3.55,
            'فاثوم' => 1.8288, 'رود' => 5.0292, 'لينك' => 0.201168, 'تشين' => 20.1168, 'ليغ' => 4828, 'بيكا' => 0.004233, 'بوينت' => 0.000352778,
        ];

        $weight = [
            'ملليجرام' => 0.001, 'سنتيجرام' => 0.01, 'ديسيجرام' => 0.1, 'جرام' => 1, 'ديكاجرام' => 10, 'هيكتوجرام' => 100,
            'كيلوجرام' => 1000, 'طن متري' => 1000000, 'أوقية' => 28.3495, 'باوند' => 453.592, 'حجر' => 6350.29, 'قيراط' => 0.2,
            'طن أمريكي' => 907185, 'طن بريطاني' => 1016047, 'رطل' => 453.592, 'حبة' => 0.06479891, 'قنطار' => 50000, 'مثقال' => 4.25,
            'وقية ذهب' => 31.1035, 'درهم' => 3.125,
        ];

        $volume = [
            'ملليلتر' => 0.001, 'سنتيلتر' => 0.01, 'ديسيلتر' => 0.1, 'لتر' => 1, 'ديكالتر' => 10, 'هيكتولتر' => 100,
            'متر مكعب' => 1000, 'إنش مكعب' => 0.0163871, 'قدم مكعب' => 28.3168, 'ياردة مكعبة' => 764.555, 'جالون أمريكي' => 3.78541,
            'جالون بريطاني' => 4.54609, 'كوارت' => 0.946353, 'باينت' => 0.473176, 'كوب' => 0.24, 'ملعقة طعام' => 0.015,
            'ملعقة شاي' => 0.005, 'برميل نفط' => 158.987, 'أوقية سائلة' => 0.0295735, 'شوال' => 50,
        ];

        $data = [
            'بت' => 0.125, 'بايت' => 1, 'كيلوبايت' => 1024, 'ميجابايت' => 1048576, 'جيجابايت' => 1073741824,
            'تيرابايت' => 1099511627776, 'بيتابايت' => 1125899906842624, 'إكسابايت' => 1152921504606846976,
            'كيلوبت' => 128, 'ميجابت' => 131072, 'جيجابت' => 134217728, 'تيرابت' => 137438953472,
            'كيبيايت' => 1024, 'ميبيبايت' => 1048576, 'جيبيبايت' => 1073741824,
        ];

        $area = [
            'متر مربع' => 1, 'سنتيمتر مربع' => 0.0001, 'ملليمتر مربع' => 0.000001, 'كيلومتر مربع' => 1000000, 'هكتار' => 10000,
            'فدان' => 4200, 'آر' => 100, 'قدم مربع' => 0.092903, 'ياردة مربعة' => 0.836127, 'إنش مربع' => 0.00064516,
            'فدان أمريكي' => 4046.86, 'ميل مربع' => 2589988.11, 'دانم' => 1000, 'قيراط أرض' => 175, 'قصبة مربعة' => 12.602,
        ];

        foreach ([
            'currency' => $currencies,
            'length' => $length,
            'weight' => $weight,
            'volume' => $volume,
            'data' => $data,
            'area' => $area,
        ] as $groupKey => $units) {
            $this->seedConversionGroup($groupKey, $units);
        }
    }

    private function seedConversionGroup(string $groupKey, array $units): void
    {
        $rows = [];

        foreach ($units as $from => $fromFactor) {
            foreach ($units as $to => $toFactor) {
                if ($from === $to) {
                    continue;
                }

                $rows[] = [
                    'group_key' => $groupKey,
                    'from_unit_ar' => $from,
                    'from_slug_ar' => ArabicSlug::make($from),
                    'to_unit_ar' => $to,
                    'to_slug_ar' => ArabicSlug::make($to),
                    'ratio' => $fromFactor / $toFactor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach (array_chunk($rows, 1000) as $chunk) {
            Conversion::query()->insert($chunk);
        }
    }

    private function tool(int $categoryId, string $name, string $type, array $schema, array $options = []): array
    {
        $settings = $options['settings'] ?? [];
        $featured = (bool) ($options['featured'] ?? false);

        return [
            'category_id' => $categoryId,
            'name_ar' => $name,
            'slug_ar' => ArabicSlug::make($name),
            'description' => 'أداة عربية لحساب ' . $name . ' مع شرح مبسط وأمثلة عملية وأسئلة شائعة وروابط داخلية.',
            'tool_type' => $type,
            'meta_title' => $name . ' | أدوات BakerySoft العربية',
            'meta_description' => 'استخدم ' . $name . ' باللغة العربية للحصول على نتائج دقيقة وسريعة مع أمثلة وشرح وFAQ.',
            'schema' => $schema,
            'settings' => $settings,
            'content' => [
                'example' => 'استخدم ' . $name . ' بإدخال القيم الأساسية ثم قارن النتيجة مع أكثر من سيناريو للوصول إلى قرار أفضل.',
                'explanation' => [
                    'تعتمد الأداة على معادلات واضحة ومخرجات قابلة للمقارنة السريعة.',
                    'تم تصميم الصفحة لتناسب نية البحث العربية وتقديم إجابة عملية مباشرة.',
                    'يمكنك استخدام النتائج للمراجعة الأولية قبل اتخاذ القرار المالي أو اليومي.',
                ],
            ],
            'faq' => [
                ['question' => 'كيف أستخدم ' . $name . '؟', 'answer' => 'أدخل القيم المطلوبة في الحقول ثم اضغط على زر الحساب لمشاهدة النتيجة الفورية.'],
                ['question' => 'هل نتائج ' . $name . ' دقيقة؟', 'answer' => 'النتيجة تقديرية ودقيقة وفق القيم التي تدخلها، لكنها لا تغني عن المراجعة المهنية في القرارات الكبيرة.'],
                ['question' => 'هل يمكن مقارنة أكثر من سيناريو؟', 'answer' => 'نعم، عدّل القيم أكثر من مرة وقارن النتائج لاختيار السيناريو الأنسب.'],
            ],
            'is_featured' => $featured,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function compoundSchema(): array
    {
        return [
            ['name' => 'principal', 'label' => 'رأس المال', 'type' => 'number', 'step' => 'any', 'default' => 10000],
            ['name' => 'rate', 'label' => 'العائد السنوي %', 'type' => 'number', 'step' => 'any', 'default' => 8],
            ['name' => 'years', 'label' => 'عدد السنوات', 'type' => 'number', 'step' => 'any', 'default' => 10],
            ['name' => 'monthly_contribution', 'label' => 'إضافة شهرية', 'type' => 'number', 'step' => 'any', 'default' => 500],
            ['name' => 'compounds_per_year', 'label' => 'مرات التركيب سنويًا', 'type' => 'number', 'step' => 1, 'default' => 12],
        ];
    }

    private function investmentSchema(): array
    {
        return [
            ['name' => 'initial_amount', 'label' => 'القيمة الابتدائية', 'type' => 'number', 'step' => 'any', 'default' => 10000],
            ['name' => 'final_amount', 'label' => 'القيمة النهائية', 'type' => 'number', 'step' => 'any', 'default' => 13800],
            ['name' => 'period_months', 'label' => 'عدد الشهور', 'type' => 'number', 'step' => 'any', 'default' => 18],
        ];
    }

    private function tradeSchema(): array
    {
        return [
            ['name' => 'quantity', 'label' => 'الكمية', 'type' => 'number', 'step' => 'any', 'default' => 100],
            ['name' => 'buy_price', 'label' => 'سعر الشراء', 'type' => 'number', 'step' => 'any', 'default' => 10],
            ['name' => 'sell_price', 'label' => 'سعر البيع', 'type' => 'number', 'step' => 'any', 'default' => 13],
            ['name' => 'fees', 'label' => 'الرسوم', 'type' => 'number', 'step' => 'any', 'default' => 25],
        ];
    }

    private function loanSchema(): array
    {
        return [
            ['name' => 'amount', 'label' => 'قيمة القرض', 'type' => 'number', 'step' => 'any', 'default' => 150000],
            ['name' => 'rate', 'label' => 'الفائدة السنوية %', 'type' => 'number', 'step' => 'any', 'default' => 6.5],
            ['name' => 'years', 'label' => 'مدة القرض بالسنوات', 'type' => 'number', 'step' => 'any', 'default' => 5],
        ];
    }

    private function vatSchema(): array
    {
        return [
            ['name' => 'amount', 'label' => 'المبلغ قبل الضريبة', 'type' => 'number', 'step' => 'any', 'default' => 1000],
            ['name' => 'rate', 'label' => 'نسبة الضريبة %', 'type' => 'number', 'step' => 'any', 'default' => 15],
        ];
    }

    private function discountSchema(): array
    {
        return [
            ['name' => 'original_price', 'label' => 'السعر الأصلي', 'type' => 'number', 'step' => 'any', 'default' => 450],
            ['name' => 'discount_rate', 'label' => 'نسبة الخصم %', 'type' => 'number', 'step' => 'any', 'default' => 20],
        ];
    }

    private function percentageSchema(): array
    {
        return [
            ['name' => 'base_value', 'label' => 'القيمة الأساسية', 'type' => 'number', 'step' => 'any', 'default' => 850],
            ['name' => 'percentage_value', 'label' => 'النسبة %', 'type' => 'number', 'step' => 'any', 'default' => 12],
        ];
    }

    private function marginSchema(): array
    {
        return [
            ['name' => 'revenue', 'label' => 'الإيرادات', 'type' => 'number', 'step' => 'any', 'default' => 120000],
            ['name' => 'cost', 'label' => 'التكلفة', 'type' => 'number', 'step' => 'any', 'default' => 86000],
        ];
    }

    private function goldSchema(): array
    {
        return [
            ['name' => 'grams', 'label' => 'الوزن بالجرام', 'type' => 'number', 'step' => 'any', 'default' => 25],
            ['name' => 'price_per_gram', 'label' => 'سعر الجرام', 'type' => 'number', 'step' => 'any', 'default' => 285],
        ];
    }

    private function inflationSchema(): array
    {
        return [
            ['name' => 'amount', 'label' => 'القيمة الحالية', 'type' => 'number', 'step' => 'any', 'default' => 1000],
            ['name' => 'rate', 'label' => 'معدل التضخم السنوي %', 'type' => 'number', 'step' => 'any', 'default' => 5],
            ['name' => 'years', 'label' => 'عدد السنوات', 'type' => 'number', 'step' => 'any', 'default' => 4],
        ];
    }

    private function salarySchema(): array
    {
        return [
            ['name' => 'monthly_salary', 'label' => 'الراتب الأساسي', 'type' => 'number', 'step' => 'any', 'default' => 10000],
            ['name' => 'allowances', 'label' => 'البدلات', 'type' => 'number', 'step' => 'any', 'default' => 1200],
            ['name' => 'deductions_rate', 'label' => 'الاستقطاعات %', 'type' => 'number', 'step' => 'any', 'default' => 9],
        ];
    }

    private function ageSchema(): array
    {
        return [
            ['name' => 'birth_date', 'label' => 'تاريخ الميلاد', 'type' => 'date', 'default' => '1995-01-01'],
        ];
    }

    private function daysSchema(): array
    {
        return [
            ['name' => 'start_date', 'label' => 'من تاريخ', 'type' => 'date', 'default' => '2025-01-01'],
            ['name' => 'end_date', 'label' => 'إلى تاريخ', 'type' => 'date', 'default' => '2025-12-31'],
        ];
    }

    private function timeSchema(): array
    {
        return [
            ['name' => 'start_time', 'label' => 'وقت البداية', 'type' => 'time', 'default' => '08:30'],
            ['name' => 'end_time', 'label' => 'وقت النهاية', 'type' => 'time', 'default' => '17:00'],
        ];
    }

    private function bmiSchema(): array
    {
        return [
            ['name' => 'weight', 'label' => 'الوزن كجم', 'type' => 'number', 'step' => 'any', 'default' => 72],
            ['name' => 'height_cm', 'label' => 'الطول سم', 'type' => 'number', 'step' => 'any', 'default' => 175],
        ];
    }

    private function savingsSchema(): array
    {
        return [
            ['name' => 'target_amount', 'label' => 'الهدف الادخاري', 'type' => 'number', 'step' => 'any', 'default' => 100000],
            ['name' => 'monthly_save', 'label' => 'الادخار الشهري', 'type' => 'number', 'step' => 'any', 'default' => 1500],
            ['name' => 'annual_return', 'label' => 'العائد السنوي %', 'type' => 'number', 'step' => 'any', 'default' => 7],
        ];
    }

    private function breakEvenSchema(): array
    {
        return [
            ['name' => 'fixed_costs', 'label' => 'التكاليف الثابتة', 'type' => 'number', 'step' => 'any', 'default' => 30000],
            ['name' => 'unit_price', 'label' => 'سعر الوحدة', 'type' => 'number', 'step' => 'any', 'default' => 120],
            ['name' => 'variable_cost', 'label' => 'تكلفة الوحدة المتغيرة', 'type' => 'number', 'step' => 'any', 'default' => 55],
        ];
    }
}
