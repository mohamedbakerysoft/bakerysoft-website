<?php

namespace Tests\Feature;

use Database\Seeders\ToolsPlatformSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ToolsPlatformSeeder::class);
    }

    public function test_home_page_renders(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('أدوات BakerySoft');
        $response->assertSee('أدوات عربية واضحة وسريعة');
    }

    public function test_tool_page_renders_with_arabic_route(): void
    {
        $response = $this->get(route('tool.show', [
            'categorySlug' => 'ادوات-الاستثمار',
            'toolSlug' => 'حاسبة-الفائدة-المركبة',
        ]));

        $response->assertOk();
        $response->assertSee('حاسبة الفائدة المركبة');
        $response->assertSee('جنيه مصري');
    }

    public function test_conversion_page_renders(): void
    {
        $response = $this->get(route('conversion.show', [
            'from' => 'دولار-أمريكي',
            'to' => 'جنيه-مصري',
            'amount' => 10,
        ]));

        $response->assertOk();
        $response->assertSee('تحويل دولار أمريكي إلى جنيه مصري');
        $response->assertSee('نتيجة التحويل');
    }

    public function test_static_pages_render(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('من نحن');

        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('سياسة الخصوصية');

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('اتصل بنا');
    }

    public function test_sitemap_and_robots_render(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8')
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertSee(route('home'), false)
            ->assertSee(route('about'), false);

        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('User-agent: *')
            ->assertSee('Sitemap: ' . route('sitemap'));
    }
}
