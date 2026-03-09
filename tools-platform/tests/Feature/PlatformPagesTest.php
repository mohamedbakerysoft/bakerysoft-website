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
        $response->assertSee('حواسب ومحولات عربية');
    }

    public function test_tool_page_renders_with_arabic_route(): void
    {
        $response = $this->get(route('tool.show', [
            'categorySlug' => 'ادوات-الاستثمار',
            'toolSlug' => 'حاسبة-الفائدة-المركبة',
        ]));

        $response->assertOk();
        $response->assertSee('حاسبة الفائدة المركبة');
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
}
