<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($tools as $tool)
        <article class="tool-card">
            <div>
                <span class="chip">{{ $tool->category->name_ar }}</span>
                <h3 class="mt-4 text-xl font-bold text-slate-900 dark:text-white">{{ $tool->name_ar }}</h3>
                <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $tool->description }}</p>
            </div>
            <div class="mt-6 flex items-center justify-between gap-3">
                <a href="{{ route('tool.show', ['categorySlug' => $tool->category->slug_ar, 'toolSlug' => $tool->slug_ar]) }}" class="btn-primary">افتح الأداة</a>
                <span class="text-xs font-semibold text-slate-400">{{ $tool->tool_type }}</span>
            </div>
        </article>
    @endforeach
</div>
