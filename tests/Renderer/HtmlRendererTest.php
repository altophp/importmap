<?php

declare(strict_types=1);

/*
 * This file is part of the ALTO library.
 *
 * © 2025–present Simon André
 *
 * For full copyright and license information, please see
 * the LICENSE file distributed with this source code.
 */

namespace Alto\ImportMap\Tests\Renderer;

use Alto\ImportMap\ImportMap;
use Alto\ImportMap\Renderer\HtmlRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HtmlRenderer::class)]
final class HtmlRendererTest extends TestCase
{
    private HtmlRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new HtmlRenderer();
    }

    public function testRenderEmpty(): void
    {
        $map = new ImportMap();
        $html = $this->renderer->render($map);

        $this->assertSame('<script type="importmap">[]</script>', $html);
    }

    public function testRenderWithImportsAndPreload(): void
    {
        $map = new ImportMap();
        $map->add('react', 'https://cdn.example.com/react.js');

        $html = $this->renderer->render($map, preload: true);

        $expectedJson = json_encode($map, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        $this->assertStringContainsString($expectedJson, $html);
        $this->assertStringContainsString('<link rel="modulepreload" href="https://cdn.example.com/react.js">', $html);
    }

    public function testRenderWithoutPreload(): void
    {
        $map = new ImportMap();
        $map->add('react', 'https://cdn.example.com/react.js');

        $html = $this->renderer->render($map, preload: false);

        $this->assertStringNotContainsString('<link rel="modulepreload"', $html);
    }

    public function testRenderEscaping(): void
    {
        $map = new ImportMap();
        $map->add('xss', '"><script>alert(1)</script>');

        $html = $this->renderer->render($map, preload: true);

        // JSON encoding handles the script tag content safety
        // htmlspecialchars handles the link href safety

        $this->assertStringContainsString('&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;', $html);
    }

    public function testRenderSkipsNullImportsInPreload(): void
    {
        $map = new ImportMap();
        $map->add('blocked', null);
        $map->add('allowed', '/js/allowed.js');

        $html = $this->renderer->render($map, preload: true);

        // Should be in JSON
        $this->assertStringContainsString('"blocked":null', $html);

        // Should NOT be in preload
        $this->assertStringNotContainsString('<link rel="modulepreload" href="">', $html);

        // Allowed one should be there
        $this->assertStringContainsString('<link rel="modulepreload" href="/js/allowed.js">', $html);
    }
}
