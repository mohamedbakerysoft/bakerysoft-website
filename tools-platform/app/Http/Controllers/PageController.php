<?php

namespace App\Http\Controllers;

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
}
