@extends('layouts.app')

@section('content')
    <section class="shell pt-10">
        <div class="grid gap-6 lg:grid-cols-[1.4fr_0.6fr]">
            <div class="card-panel px-6 py-8 md:px-10 md:py-12">
                <span class="chip">نتيجة سريعة + شرح واضح + أمثلة عملية</span>
                <h1 class="mt-5 text-4xl font-extrabold leading-tight text-slate-950 dark:text-white md:text-6xl">منصة عربية تساعدك على فهم النتيجة، لا مجرد استخراج رقم</h1>
                <p class="mt-6 max-w-3xl text-lg leading-9 text-slate-600 dark:text-slate-300">في Calclyo ستجد أدوات للحساب والتحويل والتخطيط المالي مكتوبة بالعربية، مع توضيح متى تستخدم كل أداة وكيف تقرأ الناتج وما الذي يجب الانتباه له قبل اتخاذ القرار.</p>
                <form action="{{ route('home') }}" method="get" class="mt-8 grid gap-3 md:grid-cols-[1fr_auto]">
                    <input class="field" type="search" name="q" value="{{ $query }}" placeholder="ابحث عن أداة مثل حاسبة الفائدة المركبة أو تحويل الدولار إلى الجنيه المصري">
                    <button class="btn-primary" type="submit">ابحث الآن</button>
                </form>
                <div class="mt-8 grid gap-3 text-sm font-semibold text-slate-500 dark:text-slate-400 md:grid-cols-3">
                    <div class="card-panel px-4 py-4">أدوات أساسية تخدم المال والاستثمار والاستخدام اليومي</div>
                    <div class="card-panel px-4 py-4">نتائج تفصيلية مع تفسير يساعدك على المقارنة</div>
                    <div class="card-panel px-4 py-4">صفحات عربية واضحة مع شرح ومثال وأسئلة شائعة</div>
                </div>
            </div>
            <div class="grid gap-6">
                <div class="card-panel px-6 py-8">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">الأدوات الأكثر استخدامًا</h2>
                    <div class="mt-4 space-y-3 text-sm text-slate-600 dark:text-slate-300">
                        <a class="block" href="{{ route('tool.show', ['categorySlug' => 'ادوات-الاستثمار', 'toolSlug' => 'حاسبة-الفائدة-المركبة']) }}">حاسبة الفائدة المركبة</a>
                        <a class="block" href="{{ route('tool.show', ['categorySlug' => 'ادوات-المال', 'toolSlug' => 'حاسبة-القرض']) }}">حاسبة القرض</a>
                        <a class="block" href="{{ route('tool.show', ['categorySlug' => 'الحاسبات-اليومية', 'toolSlug' => 'حاسبة-العمر']) }}">حاسبة العمر</a>
                        <a class="block" href="{{ route('conversion.show', ['from' => 'دولار-أمريكي', 'to' => 'جنيه-مصري']) }}">تحويل دولار إلى جنيه</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="shell pt-12">
        <div class="grid gap-5 md:grid-cols-3">
            <div class="card-panel px-6 py-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">كيف نستفيد من الأدوات؟</h2>
                <p class="mt-3 text-sm leading-8 text-slate-600 dark:text-slate-300">كل صفحة لا تكتفي بعرض الناتج، بل تشرح لك أين تستخدم الأداة وكيف تفسر الأرقام وما الأخطاء الشائعة التي قد تفسد القرار.</p>
            </div>
            <div class="card-panel px-6 py-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">ما نوع المحتوى الذي ستجده؟</h2>
                <p class="mt-3 text-sm leading-8 text-slate-600 dark:text-slate-300">نركز على الحاسبات المالية والاستثمارية واليومية وصفحات التحويل، مع أمثلة عملية وسيناريوهات شائعة تناسب المستخدم العربي.</p>
            </div>
            <div class="card-panel px-6 py-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">متى نستخدم النتيجة كمؤشر فقط؟</h2>
                <p class="mt-3 text-sm leading-8 text-slate-600 dark:text-slate-300">بعض الصفحات تعطي تقديرًا مرجعيًا للتخطيط والمقارنة، لذلك نوضح دائمًا أين تكفي النتيجة وأين يلزم الرجوع إلى بنك أو مختص أو جهة تنفيذ.</p>
            </div>
        </div>
    </section>

    <section class="shell pt-16">
        <h2 class="section-title">فئات الأدوات</h2>
        <p class="section-copy">اختر القسم الذي يطابق احتياجك، وستجد داخله أدوات مرتبة بعناية مع شروحات متخصصة بدل صفحات حسابية جافة أو نتائج مبهمة.</p>
        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('category.show', ['categorySlug' => $category->slug_ar]) }}" class="tool-card">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $category->name_ar }}</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $category->description }}</p>
                    </div>
                    <div class="mt-5 text-sm font-bold text-blue-600 dark:text-blue-300">{{ $category->tools_count }} أداة</div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="shell pt-16">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 class="section-title">أدوات مميزة</h2>
                <p class="section-copy">هذه صفحات نوصي بالبدء بها لأنها تعطي نتيجة عملية سريعة وتشرح لك كيف تفهمها وتحوّلها إلى قرار أو مقارنة مفيدة.</p>
            </div>
        </div>
        <div class="mt-8">
            @include('partials.tool-grid', ['tools' => $featuredTools])
        </div>
    </section>

    <section class="shell pt-16">
        <h2 class="section-title">{{ $query !== '' ? 'نتائج البحث' : 'الأدوات الأكثر طلبًا' }}</h2>
        <p class="section-copy">{{ $query !== '' ? 'نتائج مطابقة لعبارة البحث التي أدخلتها. افتح الأداة المناسبة وستجد داخلها شرحًا واضحًا للمدخلات والنتائج والحالات الشائعة للاستخدام.' : 'هذه الأدوات مرتبة لتبدأ بالأكثر طلبًا وفائدة للمستخدمين، مع صفحات تقدم تفسيرًا للنتيجة بدل الاكتفاء بإخراج رقم فقط.' }}</p>
        <div class="mt-8">
            @include('partials.tool-grid', ['tools' => $tools])
        </div>
    </section>
@endsection
