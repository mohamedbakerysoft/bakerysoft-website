@extends('layouts.app')

@section('content')
    <section class="shell pt-10">
        <div class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="card-panel px-6 py-8 md:px-8">
                <span class="chip">{{ $tool->category->name_ar }}</span>
                <h1 class="mt-4 text-4xl font-extrabold text-slate-950 dark:text-white">{{ $tool->name_ar }}</h1>
                <p class="mt-5 text-lg leading-9 text-slate-600 dark:text-slate-300">{{ $tool->description }}</p>

                <form method="get" class="mt-8 grid gap-4 md:grid-cols-2">
                    @foreach ($tool->schema ?? [] as $field)
                        <label class="block">
                            <span class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">{{ $field['label'] }}</span>
                            <input
                                class="field"
                                type="{{ $field['type'] }}"
                                name="{{ $field['name'] }}"
                                step="{{ $field['step'] ?? 'any' }}"
                                value="{{ request($field['name'], $field['default'] ?? '') }}"
                            >
                        </label>
                    @endforeach
                    <div class="md:col-span-2 flex flex-wrap gap-3">
                        <button class="btn-primary" type="submit">احسب الآن</button>
                        <a href="{{ route('tool.show', ['categorySlug' => $tool->category->slug_ar, 'toolSlug' => $tool->slug_ar]) }}" class="btn-secondary">إعادة ضبط</a>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="ad-slot">AdSense Placeholder / Top Banner</div>

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
                        <p class="mt-5 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $result['summary'] ?? '' }}</p>
                        <div class="mt-6 ad-slot">AdSense Placeholder / After Result</div>
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
                    {{ data_get($tool->content, 'example') }}
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
        <div class="ad-slot">AdSense Placeholder / Inside Content</div>
    </section>

    <section class="shell pt-12">
        <h2 class="section-title">أدوات مرتبطة</h2>
        <p class="section-copy">روابط داخلية تلقائية تساعد الزائر على الاستكشاف وتزيد كثافة الربط الداخلي بين الصفحات المتقاربة.</p>
        <div class="mt-8">
            @include('partials.tool-grid', ['tools' => $relatedTools])
        </div>
    </section>
@endsection
