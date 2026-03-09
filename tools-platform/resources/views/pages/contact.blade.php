@extends('layouts.app')

@section('content')
    <section class="shell pt-10">
        <div class="page-hero">
            <span class="chip">اتصل بنا</span>
            <h1 class="mt-4 text-4xl font-extrabold text-slate-950 dark:text-white md:text-5xl">يسعدنا استقبال ملاحظاتك واستفساراتك</h1>
            <p class="mt-5 max-w-4xl text-lg leading-9 text-slate-600 dark:text-slate-300">إذا كان لديك اقتراح لتحسين أداة، أو ملاحظة على المحتوى، أو استفسار تقني أو تجاري، يمكنك التواصل معنا عبر البريد التالي وسنراجع رسالتك في أقرب وقت ممكن.</p>
        </div>
    </section>

    <section class="shell pt-12">
        <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="card-panel px-6 py-8 md:px-8">
                <h2 class="section-title">طرق التواصل</h2>
                <div class="mt-6 space-y-4 text-sm leading-8 text-slate-600 dark:text-slate-300">
                    <div class="policy-item">
                        <h3>البريد الإلكتروني</h3>
                        <p><a class="font-bold text-blue-600 dark:text-blue-300" href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
                    </div>
                    <div class="policy-item">
                        <h3>نوع الرسائل التي نرحب بها</h3>
                        <p>اقتراحات الأدوات الجديدة، ملاحظات تحسين الواجهة، الإبلاغ عن أخطاء، الاستفسارات التجارية، وملاحظات تحسين الدقة أو الصياغة.</p>
                    </div>
                    <div class="policy-item">
                        <h3>وقت الاستجابة</h3>
                        <p>نحاول مراجعة الرسائل خلال أيام العمل، وقد تختلف مدة الرد بحسب طبيعة الطلب وحجم الرسائل الواردة.</p>
                    </div>
                </div>
            </div>

            <div class="card-panel px-6 py-8">
                <h2 class="section-title">قبل التواصل</h2>
                <div class="mt-6 space-y-4 text-sm leading-8 text-slate-600 dark:text-slate-300">
                    <div class="policy-item">
                        <h3>للإبلاغ عن مشكلة</h3>
                        <p>اذكر اسم الأداة أو رابط الصفحة، والقيم التي أدخلتها، والنتيجة التي ظهرت لك، حتى نتمكن من فحص المشكلة بسرعة.</p>
                    </div>
                    <div class="policy-item">
                        <h3>لطلب أداة جديدة</h3>
                        <p>أرسل اسم الأداة المطلوبة، ومن تستهدفه، وما أهم المدخلات والنتائج التي تتوقع ظهورها داخل الصفحة.</p>
                    </div>
                    <div class="policy-item">
                        <h3>للتعاون التجاري</h3>
                        <p>يمكنك توضيح نوع التعاون المطلوب، والمجال، وطبيعة العرض، وسنراجع التفاصيل عند استلامها.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
