<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Conversion;
use App\Models\Tool;
use App\Services\ToolCalculator;
use Illuminate\Http\Request;

class ConversionController extends Controller
{
    public function __invoke(Request $request, string $from, string $to, ToolCalculator $calculator)
    {
        $conversion = Conversion::where('from_slug_ar', $from)
            ->where('to_slug_ar', $to)
            ->firstOrFail();

        $amount = (float) $request->query('amount', 1);
        $result = $calculator->convertPage($conversion, $amount);
        $category = Category::where('slug_ar', 'المحولات')->first();
        $relatedTools = Tool::with('category')
            ->whereBelongsTo($category)
            ->take(6)
            ->get();
        $nearbyConversions = Conversion::where('group_key', $conversion->group_key)
            ->where('from_slug_ar', $conversion->from_slug_ar)
            ->whereKeyNot($conversion->id)
            ->take(8)
            ->get();

        return view('conversion', [
            'conversion' => $conversion,
            'amount' => $amount,
            'result' => $result,
            'relatedTools' => $relatedTools,
            'nearbyConversions' => $nearbyConversions,
            'metaTitle' => 'تحويل ' . $conversion->from_unit_ar . ' إلى ' . $conversion->to_unit_ar . ' | BakerySoft',
            'metaDescription' => 'حوّل بسهولة من ' . $conversion->from_unit_ar . ' إلى ' . $conversion->to_unit_ar . ' مع شرح وأمثلة وأسئلة شائعة وروابط داخلية.',
        ]);
    }
}
