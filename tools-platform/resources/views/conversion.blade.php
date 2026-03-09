@extends('layouts.app')

@section('content')
    <section class="shell pt-10">
        <div class="grid gap-8 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="card-panel px-6 py-8 md:px-8">
                <span class="chip">صفحة SEO برمجية</span>
                <h1 class="mt-4 text-4xl font-extrabold text-slate-950 dark:text-white">تحويل {{ $conversion->from_unit_ar }} إلى {{ $conversion->to_unit_ar }}</h1>
                <p class="mt-5 text-lg leading-9 text-slate-600 dark:text-slate-300">استخدم محول {{ $conversion->from_unit_ar }} إلى {{ $conversion->to_unit_ar }} للحصول على تحويل مباشر وسريع مع شرح مبسط وروابط إضافية لنفس الوحدة المصدر.</p>

                <form method="get" class="mt-8 grid gap-4 md:grid-cols-[1fr_auto]">
                    <label class="block">
                        <span class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">القيمة</span>
                        <input class="field" type="number" step="any" name="amount" value="{{ request('amount', $amount) }}">
                    </label>
                    <button class="btn-primary self-end" type="submit">حوّل الآن</button>
                </form>
            </div>

            <div class="space-y-6">
                <div class="ad-slot">AdSense Placeholder / Top Banner</div>
                <div class="card-panel px-6 py-8">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">نتيجة التحويل</h2>
                    <div class="mt-5 space-y-4">
                        @foreach ($result['rows'] as $row)
                            <div class="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $row['label'] }}</span>
                                <span class="text-base font-extrabold text-slate-900 dark:text-white">{{ $row['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-5 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $result['summary'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="shell pt-12">
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="card-panel px-6 py-8">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">طريقة الاستخدام</h2>
                <div class="mt-5 space-y-4 text-sm leading-8 text-slate-600 dark:text-slate-300">
                    <p>أدخل القيمة التي تريد تحويلها من {{ $conversion->from_unit_ar }} إلى {{ $conversion->to_unit_ar }}، ثم اضغط على زر التحويل.</p>
                    <p>هذه الصفحة جزء من بنية SEO برمجية تمكّن الموقع من تغطية آلاف تراكيب التحويل المختلفة مع محتوى مفيد وليس مجرد صفحة فارغة.</p>
                    <p>يمكنك أيضًا الانتقال إلى تحويلات أخرى من نفس الوحدة المصدر عبر الروابط ذات الصلة أدناه.</p>
                </div>
            </div>
            <div class="card-panel px-6 py-8">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">أسئلة شائعة</h2>
                <div class="mt-5 space-y-4">
                    <details class="rounded-2xl border border-slate-200 px-4 py-4 dark:border-slate-800">
                        <summary class="cursor-pointer text-sm font-bold text-slate-900 dark:text-white">هل هذا التحويل ثابت؟</summary>
                        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">في الوحدات القياسية يكون ثابتًا، أما العملات فهي تقريبية ويمكن تحديثها دوريًا حسب مصدر الأسعار.</p>
                    </details>
                    <details class="rounded-2xl border border-slate-200 px-4 py-4 dark:border-slate-800">
                        <summary class="cursor-pointer text-sm font-bold text-slate-900 dark:text-white">كيف أستخدم هذه الصفحة في المقارنة؟</summary>
                        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">بدّل القيمة أكثر من مرة، وجرّب الروابط المجاورة للوصول إلى وجهات تحويل مختلفة من نفس الوحدة.</p>
                    </details>
                </div>
            </div>
        </div>
    </section>

    <section class="shell pt-12">
        <div class="ad-slot">AdSense Placeholder / Inside Content</div>
    </section>

    <section class="shell pt-12">
        <div class="grid gap-8 lg:grid-cols-2">
            <div>
                <h2 class="section-title">تحويلات مرتبطة</h2>
                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    @foreach ($nearbyConversions as $item)
                        <a href="{{ route('conversion.show', ['from' => $item->from_slug_ar, 'to' => $item->to_slug_ar]) }}" class="tool-card">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $item->from_unit_ar }} إلى {{ $item->to_unit_ar }}</h3>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">تحويل سريع مع نفس الوحدة المصدر.</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div>
                <h2 class="section-title">أدوات من فئة المحولات</h2>
                <div class="mt-8">
                    @include('partials.tool-grid', ['tools' => $relatedTools])
                </div>
            </div>
        </div>
    </section>
@endsection
