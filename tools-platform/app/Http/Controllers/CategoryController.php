<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function __invoke(string $categorySlug)
    {
        $category = Category::where('slug_ar', $categorySlug)->firstOrFail();
        $tools = $category->tools()->latest()->paginate(24);
        $relatedCategories = Category::whereKeyNot($category->id)->withCount('tools')->get();

        return view('category', [
            'category' => $category,
            'tools' => $tools,
            'relatedCategories' => $relatedCategories,
            'metaTitle' => $category->name_ar . ' | أدوات Calclyo',
            'metaDescription' => $category->description ?: ('تصفح ' . $category->name_ar . ' في منصة Calclyo العربية للأدوات والحاسبات.'),
        ]);
    }
}
