<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Conversion;
use App\Models\Tool;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', [
            'metaTitle' => 'من نحن | أدوات BakerySoft',
            'metaDescription' => 'تعرف على منصة أدوات BakerySoft العربية ورسالتها في تقديم حاسبات ومحولات واضحة وسريعة للمستخدم العربي.',
        ]);
    }

    public function privacy()
    {
        return view('pages.privacy', [
            'metaTitle' => 'سياسة الخصوصية | أدوات BakerySoft',
            'metaDescription' => 'سياسة الخصوصية الخاصة بموقع أدوات BakerySoft وكيفية التعامل مع البيانات وملفات تعريف الارتباط والخدمات الإعلانية.',
        ]);
    }

    public function contact()
    {
        return view('pages.contact', [
            'metaTitle' => 'اتصل بنا | أدوات BakerySoft',
            'metaDescription' => 'طرق التواصل مع فريق أدوات BakerySoft للاستفسارات التقنية والتجارية وملاحظات تحسين الموقع.',
            'supportEmail' => 'support@bakerysoft.net',
        ]);
    }

    public function sitemap(): Response
    {
        $urls = collect([
            [
                'loc' => route('home'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('about'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('privacy'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('contact'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
        ]);

        $categoryUrls = Category::query()
            ->get()
            ->map(fn (Category $category) => [
                'loc' => route('category.show', ['categorySlug' => $category->slug_ar]),
                'lastmod' => $category->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ]);

        $toolUrls = Tool::with('category')
            ->get()
            ->map(fn (Tool $tool) => [
                'loc' => route('tool.show', ['categorySlug' => $tool->category->slug_ar, 'toolSlug' => $tool->slug_ar]),
                'lastmod' => $tool->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);

        $conversionUrls = Conversion::query()
            ->get()
            ->map(fn (Conversion $conversion) => [
                'loc' => route('conversion.show', ['from' => $conversion->from_slug_ar, 'to' => $conversion->to_slug_ar]),
                'lastmod' => $conversion->updated_at?->toAtomString() ?? now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($urls->merge($categoryUrls)->merge($toolUrls)->merge($conversionUrls) as $url) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>' . e($url['loc']) . '</loc>';
            $xml[] = '    <lastmod>' . e($url['lastmod']) . '</lastmod>';
            $xml[] = '    <changefreq>' . e($url['changefreq']) . '</changefreq>';
            $xml[] = '    <priority>' . e($url['priority']) . '</priority>';
            $xml[] = '  </url>';
        }

        $xml[] = '</urlset>';

        return response(implode("\n", $xml), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    public function robots(): Response
    {
        return response(implode("\n", [
            'User-agent: *',
            'Allow: /',
            '',
            'Sitemap: ' . route('sitemap'),
        ]), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function ads(): Response
    {
        return response('google.com, pub-7475653835852794, DIRECT, f08c47fec0942fa0', 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
