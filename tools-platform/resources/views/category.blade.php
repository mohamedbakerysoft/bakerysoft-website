@extends('layouts.app')

@section('content')
    <section class="shell pt-10">
        <div class="card-panel px-6 py-8 md:px-10">
            <span class="chip">{{ $category->name_ar }}</span>
            <h1 class="mt-4 text-4xl font-extrabold text-slate-950 dark:text-white">{{ $category->headline ?: $category->name_ar }}</h1>
            <p class="mt-5 max-w-4xl text-lg leading-9 text-slate-600 dark:text-slate-300">{{ $category->description }}</p>
            <div class="mt-6 flex flex-wrap gap-3 text-sm font-semibold text-slate-500 dark:text-slate-400">
                <span class="rounded-full bg-slate-100 px-4 py-2 dark:bg-slate-800">{{ $tools->total() }} أداة داخل هذا القسم</span>
                <span class="rounded-full bg-slate-100 px-4 py-2 dark:bg-slate-800">روابط داخلية وأدوات مرتبطة لتسهيل الاستكشاف</span>
            </div>
        </div>
    </section>

    <section class="shell pt-12">
        <div class="grid gap-8 xl:grid-cols-[1fr_320px]">
            <div>
                @include('partials.tool-grid', ['tools' => $tools])
                <div class="mt-8">{{ $tools->links() }}</div>
            </div>
            <aside class="space-y-6">
                <div class="card-panel px-6 py-6">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">أقسام مرتبطة</h2>
                    <div class="mt-4 space-y-3 text-sm leading-7 text-slate-600 dark:text-slate-300">
                        @foreach ($relatedCategories as $relatedCategory)
                            <a class="block" href="{{ route('category.show', ['categorySlug' => $relatedCategory->slug_ar]) }}">
                                {{ $relatedCategory->name_ar }} ({{ $relatedCategory->tools_count }})
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
