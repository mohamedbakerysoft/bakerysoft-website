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
        $response->assertSee('أدوات Calclyo');
        $response->assertSee('منصة عربية تساعدك على فهم النتيجة');
        $response->assertSee('<link rel="canonical" href="http://localhost">', false);
        $response->assertSee('<meta name="robots" content="index,follow">', false);
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
        $response->assertSee('index,follow');
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
        $response->assertSee('مرجعية');
        $response->assertSee('noindex,follow');
    }

    public function test_non_priority_conversion_page_is_noindexed(): void
    {
        $response = $this->get(route('conversion.show', [
            'from' => 'أوقية-موريتانية',
            'to' => 'بات-تايلندي',
        ]));

        $response->assertOk();
        $response->assertSee('تحويل أوقية موريتانية إلى بات تايلندي');
        $response->assertSee('<meta name="robots" content="noindex,follow">', false);
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
            ->assertSee('اتصل بنا')
            ->assertSee('mohamed.bakerysoft@gmail.com');
    }

    public function test_sitemap_and_robots_render(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8')
            ->assertHeader('cache-control', 'max-age=3600, public')
            ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
            ->assertSee(route('home'), false)
            ->assertSee(route('about'), false)
            ->assertSee(route('conversion.show', ['from' => 'دولار-أمريكي', 'to' => 'جنيه-مصري']), false)
            ->assertDontSee(route('conversion.show', ['from' => 'أوقية-موريتانية', 'to' => 'بات-تايلندي']), false);

        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertHeader('cache-control', 'max-age=3600, public')
            ->assertSee('User-agent: *')
            ->assertSee('Sitemap: ' . route('sitemap'));

        $this->get(route('ads'))
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertHeader('cache-control', 'max-age=3600, public')
            ->assertSee('google.com, pub-7475653835852794, DIRECT, f08c47fec0942fa0');
    }
}
