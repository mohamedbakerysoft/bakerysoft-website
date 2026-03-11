<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle ?? 'أدوات Calclyo العربية' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'منصة أدوات عربية للحاسبات والمحولات والمال والاستثمار.' }}">
    <meta name="robots" content="{{ $metaRobots ?? 'index,follow' }}">
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('brand/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7475653835852794" crossorigin="anonymous"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-GF64VM5L8V"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-GF64VM5L8V');
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'أدوات Calclyo',
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/?q={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ],
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($websiteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
</head>
<body x-data="themeToggle" x-init="init">
    <div class="relative overflow-hidden bg-[radial-gradient(circle_at_top_right,_rgba(37,99,235,0.16),_transparent_30%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] dark:bg-[radial-gradient(circle_at_top_right,_rgba(59,130,246,0.2),_transparent_28%),linear-gradient(180deg,#020617_0%,#0f172a_100%)]">
        <header class="shell sticky top-0 z-30 pt-4">
            <div class="card-panel flex items-center justify-between gap-4 px-5 py-4 backdrop-blur">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('brand/logo-horizontal.svg') }}" alt="Calclyo" width="420" height="88" class="hidden h-10 w-auto dark:brightness-110 md:block">
                    <span class="flex items-center gap-3 md:hidden">
                        <img src="{{ asset('brand/logo-mark.svg') }}" alt="شعار Calclyo" width="96" height="96" class="h-11 w-11 shrink-0 rounded-2xl">
                        <span class="text-lg font-extrabold text-slate-900 dark:text-white">كالكليو</span>
                    </span>
                </a>
                <nav class="hidden items-center gap-5 text-sm font-semibold text-slate-600 dark:text-slate-300 md:flex">
                    <a href="{{ route('category.show', ['categorySlug' => 'ادوات-الاستثمار']) }}">أدوات الاستثمار</a>
                    <a href="{{ route('category.show', ['categorySlug' => 'ادوات-المال']) }}">أدوات المال</a>
                    <a href="{{ route('category.show', ['categorySlug' => 'المحولات']) }}">المحولات</a>
                    <a href="{{ route('category.show', ['categorySlug' => 'الحاسبات-اليومية']) }}">الحاسبات اليومية</a>
                    <a href="{{ route('about') }}">من نحن</a>
                </nav>
                <button type="button" class="btn-secondary !px-4 !py-2" @click="toggle">
                    <span x-show="!dark">الوضع الداكن</span>
                    <span x-show="dark">الوضع الفاتح</span>
                </button>
            </div>
        </header>

        <main class="pb-16">
            @yield('content')
        </main>

        <footer class="shell pb-10">
            <div class="card-panel grid gap-8 px-6 py-8 md:grid-cols-4">
                <div>
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('brand/logo-mark.svg') }}" alt="شعار Calclyo" width="96" height="96" class="h-11 w-11 shrink-0 rounded-2xl">
                        <div>
                            <div class="text-lg font-extrabold text-slate-900 dark:text-white">كالكليو</div>
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Calclyo</div>
                        </div>
                    </div>
                    <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">منصة عربية عملية تساعد المستخدم على الحساب والتحويل بسرعة، مع واجهات واضحة ونتائج قابلة للفهم من أول نظرة.</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 dark:text-white">الأقسام</h4>
                    <div class="mt-3 space-y-2 text-sm text-slate-600 dark:text-slate-300">
                        <a class="block" href="{{ route('category.show', ['categorySlug' => 'ادوات-الاستثمار']) }}">أدوات الاستثمار</a>
                        <a class="block" href="{{ route('category.show', ['categorySlug' => 'ادوات-المال']) }}">أدوات المال</a>
                        <a class="block" href="{{ route('category.show', ['categorySlug' => 'المحولات']) }}">المحولات</a>
                        <a class="block" href="{{ route('category.show', ['categorySlug' => 'الحاسبات-اليومية']) }}">الحاسبات اليومية</a>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 dark:text-white">صفحات مهمة</h4>
                    <div class="mt-3 space-y-2 text-sm text-slate-600 dark:text-slate-300">
                        <a class="block" href="{{ route('about') }}">من نحن</a>
                        <a class="block" href="{{ route('privacy') }}">سياسة الخصوصية</a>
                        <a class="block" href="{{ route('contact') }}">اتصل بنا</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
