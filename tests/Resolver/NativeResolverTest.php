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

namespace Alto\ImportMap\Tests\Resolver;

use Alto\ImportMap\Exception\SpecifierNotFoundException;
use Alto\ImportMap\ImportMap;
use Alto\ImportMap\Resolver\NativeResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NativeResolver::class)]
final class NativeResolverTest extends TestCase
{
    private NativeResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new NativeResolver();
    }

    public function testResolveDirectMatch(): void
    {
        $map = new ImportMap();
        $map->add('react', 'https://cdn.example.com/react.js');

        $result = $this->resolver->resolve('react', $map);

        $this->assertSame([
            'url' => 'https://cdn.example.com/react.js',
            'integrity' => null,
        ], $result);
    }

    public function testResolvePrefixMatch(): void
    {
        $map = new ImportMap();
        $map->add('app/', '/js/app/');
        $map->add('app/utils/', '/js/utils/'); // More specific

        // Should match 'app/utils/' (longest prefix)
        $result = $this->resolver->resolve('app/utils/format.js', $map);
        $this->assertSame([
            'url' => '/js/utils/format.js',
            'integrity' => null,
        ], $result);

        // Should match 'app/'
        $result = $this->resolver->resolve('app/main.js', $map);
        $this->assertSame([
            'url' => '/js/app/main.js',
            'integrity' => null,
        ], $result);
    }

    public function testResolveUrlPassthrough(): void
    {
        $map = new ImportMap();

        $url = 'https://cdn.example.com/lib.js';
        $result = $this->resolver->resolve($url, $map);

        $this->assertSame([
            'url' => $url,
            'integrity' => null,
        ], $result);

        $path = '/local/script.js';
        $result = $this->resolver->resolve($path, $map);

        $this->assertSame([
            'url' => $path,
            'integrity' => null,
        ], $result);
    }

    public function testResolveIntegrity(): void
    {
        $map = new ImportMap();
        $map->add('react', 'https://cdn.example.com/react.js', 'sha384-abc');

        $result = $this->resolver->resolve('react', $map);

        $this->assertSame([
            'url' => 'https://cdn.example.com/react.js',
            'integrity' => 'sha384-abc',
        ], $result);
    }

    public function testResolveWithScope(): void
    {
        $map = new ImportMap();

        // Global
        $map->add('lodash', '/js/lodash.js');
        $map->add('moment', '/js/moment.js');

        // Scope: /admin/
        $map->add('lodash', '/js/admin/lodash.js', scope: '/admin/');

        // Scope: /admin/dashboard/ (Nested)
        $map->add('lodash', '/js/admin/dashboard/lodash.js', scope: '/admin/dashboard/');

        // Case 1: Matching Scope (/admin/)
        $result = $this->resolver->resolve('lodash', $map, '/admin/app.js');
        $this->assertSame(['url' => '/js/admin/lodash.js', 'integrity' => null], $result);

        // Case 2: Matching Nested Scope (/admin/dashboard/) - Longest match wins
        $result = $this->resolver->resolve('lodash', $map, '/admin/dashboard/widget.js');
        $this->assertSame(['url' => '/js/admin/dashboard/lodash.js', 'integrity' => null], $result);

        // Case 3: Fallback to global (scope matches /admin/, but 'moment' not in scope)
        $result = $this->resolver->resolve('moment', $map, '/admin/app.js');
        $this->assertSame(['url' => '/js/moment.js', 'integrity' => null], $result);

        // Case 4: No scope match
        $result = $this->resolver->resolve('lodash', $map, '/public/app.js');
        $this->assertSame(['url' => '/js/lodash.js', 'integrity' => null], $result);
    }

    public function testSpecifierNotFound(): void
    {
        $map = new ImportMap();

        $this->expectException(SpecifierNotFoundException::class);
        $this->expectExceptionMessage("The specifier 'unknown-package' could not be resolved.");

        $this->resolver->resolve('unknown-package', $map);
    }

    public function testRelativeSpecifiersPassThrough(): void
    {
        $map = new ImportMap();

        // ./ should be allowed as a URL-like specifier
        $result = $this->resolver->resolve('./utils.js', $map);
        $this->assertSame('./utils.js', $result['url']);

        // ../ should be allowed
        $result = $this->resolver->resolve('../utils.js', $map);
        $this->assertSame('../utils.js', $result['url']);
    }

    public function testNullMappingBlocksResolution(): void
    {
        $map = new ImportMap();
        $map->add('blocked-package', null);

        $this->expectException(\Alto\ImportMap\Exception\ResolutionFailedException::class);
        $this->expectExceptionMessage("Resolution blocked by null entry for 'blocked-package'.");

        $this->resolver->resolve('blocked-package', $map);
    }
}
