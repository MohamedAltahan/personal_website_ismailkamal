<?php

namespace Tests\Unit;

use App\Support\Blocks\BlockRegistry;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BlockRegistryTest extends TestCase
{
    public function test_unknown_block_types_are_rejected(): void
    {
        $this->expectException(ValidationException::class);

        BlockRegistry::sanitize([['type' => 'evil', 'data' => []]]);
    }

    public function test_html_is_sanitised_and_schema_enforced(): void
    {
        $blocks = BlockRegistry::sanitize([[
            'id' => 'b_abcdef1234',
            'type' => 'text',
            'data' => [
                'html' => ['ar' => '<p onclick="x()">مرحبا<script>alert(1)</script></p>', 'en' => '<p style="color:red;text-align:center">Hi <a href="javascript:alert(1)">x</a></p>'],
                'size' => 'lg',
                'unexpected' => 'dropped',
            ],
            'style' => ['bg' => '#000000', 'hacker' => true],
        ]]);

        $block = $blocks[0];
        $this->assertSame('b_abcdef1234', $block['id']);
        $this->assertSame('<p>مرحبا</p>', $block['data']['html']['ar']);
        $this->assertStringContainsString('style="text-align: center"', $block['data']['html']['en']);
        $this->assertStringNotContainsString('javascript', $block['data']['html']['en']);
        $this->assertStringNotContainsString('color', $block['data']['html']['en']);
        $this->assertArrayNotHasKey('unexpected', $block['data']);
        $this->assertArrayNotHasKey('hacker', $block['style']);
        $this->assertSame('#000000', $block['style']['bg']);
    }

    public function test_media_ids_are_collected_at_any_depth(): void
    {
        $blocks = BlockRegistry::sanitize([
            ['type' => 'image', 'data' => ['media' => '5']],
            ['type' => 'gallery', 'data' => ['items' => [['media' => 7], ['media' => 8], ['media' => 'nope']]]],
            ['type' => 'video', 'data' => ['media' => 9, 'poster' => 10]],
            ['type' => 'before_after', 'data' => ['before' => 11, 'after' => 12]],
        ]);

        $this->assertSame([5, 7, 8, 9, 10, 11, 12], BlockRegistry::mediaIds($blocks));
        $this->assertCount(2, $blocks[1]['data']['items'], 'Gallery items without media are dropped');
    }

    public function test_every_block_type_has_a_frontend_view_and_inspector(): void
    {
        foreach (array_keys(BlockRegistry::types()) as $type) {
            $this->assertTrue(view()->exists('blocks.'.$type), "Missing blocks.$type view");
            $this->assertTrue(view()->exists('admin.builder.inspectors.'.$type), "Missing inspector for $type");
        }
    }
}
