<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tool;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = trim((string) $request->string('q'));
        $categoryOrder = [
            'ادوات-الاستثمار',
            'ادوات-المال',
            'المحولات',
            'الحاسبات-اليومية',
        ];

        $categories = Category::withCount('tools')->get()->sortBy(
            fn (Category $category) => array_search($category->slug_ar, $categoryOrder, true)
        )->values();

        $featuredTools = Tool::with('category')
            ->where('is_featured', true)
            ->popular()
            ->take(12)
            ->get();

        $popularTools = Tool::with('category')
            ->when($query !== '', fn ($builder) => $builder->where('name_ar', 'like', '%' . $query . '%'))
            ->popular()
            ->take(24)
            ->get();

        return view('home', [
            'categories' => $categories,
            'featuredTools' => $featuredTools,
            'tools' => $popularTools,
            'query' => $query,
            'metaTitle' => 'أدوات Calclyo العربية للحاسبات والمحولات والاستثمار',
            'metaDescription' => 'منصة عربية تضم مئات الحاسبات والمحولات وأدوات المال والاستثمار مع صفحات SEO داخلية قابلة للتوسع.',
            'canonicalUrl' => route('home'),
            'metaRobots' => $query !== '' ? 'noindex,follow' : 'index,follow',
        ]);
    }
}
