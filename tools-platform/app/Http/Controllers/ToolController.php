<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tool;
use App\Services\ToolCalculator;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function __invoke(Request $request, string $categorySlug, string $toolSlug, ToolCalculator $calculator)
    {
        $category = Category::where('slug_ar', $categorySlug)->firstOrFail();
        $tool = Tool::with('category')
            ->where('category_id', $category->id)
            ->where('slug_ar', $toolSlug)
            ->firstOrFail();

        $result = $request->query() ? $calculator->calculate($tool, $request->query()) : [];
        $relatedTools = Tool::with('category')
            ->where('category_id', $category->id)
            ->whereKeyNot($tool->id)
            ->inRandomOrder()
            ->take(6)
            ->get();

        return view('tool', [
            'tool' => $tool,
            'result' => $result,
            'relatedTools' => $relatedTools,
            'metaTitle' => $tool->meta_title,
            'metaDescription' => $tool->meta_description,
            'canonicalUrl' => route('tool.show', ['categorySlug' => $tool->category->slug_ar, 'toolSlug' => $tool->slug_ar]),
            'metaRobots' => $request->query() ? 'noindex,follow' : 'index,follow',
        ]);
    }
}
