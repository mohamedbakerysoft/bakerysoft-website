@extends('layouts.app')

@section('content')
    <section class="shell pt-10">
        <div class="mb-5 flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
            <a href="{{ route('home') }}">الرئيسية</a>
            <span>/</span>
            <a href="{{ route('category.show', ['categorySlug' => $tool->category->slug_ar]) }}">{{ $tool->category->name_ar }}</a>
            <span>/</span>
            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $tool->name_ar }}</span>
        </div>
        <div class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="card-panel px-6 py-8 md:px-8">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="chip">{{ $tool->category->name_ar }}</span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $tool->typeLabel() }}</span>
                </div>
                <h1 class="mt-4 text-4xl font-extrabold text-slate-950 dark:text-white">{{ $tool->name_ar }}</h1>
                <p class="mt-5 text-lg leading-9 text-slate-600 dark:text-slate-300">{{ $tool->description }}</p>
                <div class="mt-6 rounded-3xl bg-slate-50 px-5 py-5 text-sm leading-8 text-slate-600 dark:bg-slate-950 dark:text-slate-300">
                    <p class="font-bold text-slate-900 dark:text-white">كيف تستخدم هذه الأداة؟</p>
                    <p class="mt-2">املأ الحقول المطلوبة بالقيم التقريبية أو الفعلية، ثم اضغط على "احسب الآن" للحصول على نتيجة واضحة وملخص سريع يساعدك على اتخاذ القرار.</p>
                </div>

                <form method="get" class="mt-8 grid gap-4 md:grid-cols-2">
                    @foreach ($tool->schema ?? [] as $field)
                        <label class="block">
                            <span class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{{ $field['label'] }}</span>
                            @if (($field['type'] ?? 'text') === 'select')
                                <select class="field" name="{{ $field['name'] }}">
                                    @foreach ($field['options'] ?? [] as $option)
                                        <option value="{{ $option['value'] }}" @selected(request($field['name'], $field['default'] ?? '') == $option['value'])>{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    class="field"
                                    type="{{ $field['type'] }}"
                                    name="{{ $field['name'] }}"
                                    @if (isset($field['step'])) step="{{ $field['step'] }}" @endif
                                    value="{{ request($field['name'], $field['default'] ?? '') }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                >
                            @endif
                            @if (! empty($field['hint']))
                                <span class="mt-2 block text-xs leading-6 text-slate-500 dark:text-slate-400">{{ $field['hint'] }}</span>
                            @endif
                        </label>
                    @endforeach
                    <div class="md:col-span-2 flex flex-wrap gap-3">
                        <button class="btn-primary" type="submit">احسب الآن</button>
                        <a href="{{ route('tool.show', ['categorySlug' => $tool->category->slug_ar, 'toolSlug' => $tool->slug_ar]) }}" class="btn-secondary">إعادة ضبط</a>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                @if ($result)
                    <div class="card-panel px-6 py-8">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">النتيجة</h2>
                        <div class="mt-5 space-y-4">
                            @foreach ($result['rows'] ?? [] as $row)
                                <div class="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3 dark:bg-slate-950">
                                    <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $row['label'] }}</span>
                                    <span class="text-base font-extrabold text-slate-900 dark:text-white">{{ $row['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-5 text-sm leading-8 text-slate-600 dark:text-slate-300">{{ $result['summary'] ?? '' }}</p>
                    </div>
                @else
                    <div class="card-panel px-6 py-8">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">ابدأ بالحساب</h2>
                        <p class="mt-4 text-sm leading-8 text-slate-600 dark:text-slate-300">أدخل القيم في النموذج المقابل ثم اضغط على "احسب الآن". ستظهر النتيجة هنا مع ملخص سريع يساعدك على فهم الناتج بدون خطوات إضافية.</p>
                    </div>
                @endif

                @if (! empty($result['chart']))
                    <div class="card-panel px-6 py-8">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">منحنى النمو</h2>
                        <div class="mt-5 h-72">
                            <canvas id="result-chart" data-points='@json($result['chart'])'></canvas>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="shell pt-12">
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="card-panel px-6 py-8">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">شرح الأداة</h2>
                <div class="mt-5 space-y-4 text-sm leading-8 text-slate-600 dark:text-slate-300">
                    @foreach (data_get($tool->content, 'explanation', []) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
                <div class="mt-6 rounded-3xl bg-blue-50 px-5 py-5 text-sm leading-8 text-blue-900 dark:bg-blue-950/30 dark:text-blue-100">
                    <span class="block font-bold">مثال سريع</span>
                    <span class="mt-2 block">{{ data_get($tool->content, 'example') }}</span>
                </div>
            </div>
            <div class="card-panel px-6 py-8">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">الأسئلة الشائعة</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($tool->faq ?? [] as $faq)
                        <details class="rounded-2xl border border-slate-200 px-4 py-4 dark:border-slate-800">
                            <summary class="cursor-pointer text-sm font-bold text-slate-900 dark:text-white">{{ $faq['question'] }}</summary>
                            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="shell pt-12">
        <h2 class="section-title">أدوات مرتبطة</h2>
        <p class="section-copy">إذا كانت هذه الأداة قريبة من احتياجك، فالأدوات التالية ستساعدك على المقارنة أو إكمال نفس المهمة بشكل أسرع.</p>
        <div class="mt-8">
            @include('partials.tool-grid', ['tools' => $relatedTools])
        </div>
    </section>
@endsection
