<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $page = Page::create([
            'slug'    => 'syarat-ketentuan',
            'title'   => 'Syarat & Ketentuan',
            'content' => json_encode(['sections' => []], JSON_UNESCAPED_UNICODE),
        ]);

        $this->assertEquals('syarat-ketentuan', $page->slug);
        $this->assertEquals('Syarat & Ketentuan', $page->title);
    }

    public function test_find_by_slug_or_default_returns_existing(): void
    {
        Page::create([
            'slug'    => 'privasi',
            'title'   => 'Kebijakan Privasi',
            'content' => json_encode(['sections' => [['title' => 'A', 'intro' => 'B', 'items' => []]]]),
        ]);

        $page = Page::findBySlugOrDefault('privasi', 'Default Title');

        $this->assertEquals('privasi', $page->slug);
        $this->assertEquals('Kebijakan Privasi', $page->title);
    }

    public function test_find_by_slug_or_default_returns_new_instance(): void
    {
        $page = Page::findBySlugOrDefault('tidak-ada', 'Default Title');

        $this->assertInstanceOf(Page::class, $page);
        $this->assertEquals('tidak-ada', $page->slug);
        $this->assertEquals('Default Title', $page->title);
        $this->assertTrue($page->updated_at->isCurrentMinute());
    }
}