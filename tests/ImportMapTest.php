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

namespace Alto\ImportMap\Tests;

use Alto\ImportMap\Exception\InvalidEntryException;
use Alto\ImportMap\ImportMap;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ImportMap::class)]
final class ImportMapTest extends TestCase
{
    public function testAddImport(): void
    {
        $map = new ImportMap();
        $map->add('react', 'https://cdn.example.com/react.js');

        $this->assertSame([
            'react' => 'https://cdn.example.com/react.js',
        ], $map->imports);
    }

    public function testAddScopedImport(): void
    {
        $map = new ImportMap();
        $map->add('react', 'https://cdn.example.com/v1/react.js', scope: '/scope/');

        $this->assertSame([
            '/scope/' => [
                'react' => 'https://cdn.example.com/v1/react.js',
            ],
        ], $map->scopes);
    }

    public function testAddIntegrity(): void
    {
        $map = new ImportMap();
        $map->add('react', 'https://cdn.example.com/react.js', 'sha384-abc');

        $this->assertSame([
            'https://cdn.example.com/react.js' => 'sha384-abc',
        ], $map->integrity);
    }

    public function testMerge(): void
    {
        $map1 = new ImportMap(imports: ['a' => 'url-a']);
        $map2 = new ImportMap(imports: ['b' => 'url-b']);

        $map1->merge($map2);

        $this->assertSame([
            'a' => 'url-a',
            'b' => 'url-b',
        ], $map1->imports);
    }

    public function testJsonSerialize(): void
    {
        $map = new ImportMap(
            imports: ['react' => 'https://cdn.example.com/react.js'],
            scopes: ['/scope/' => ['lodash' => 'https://cdn.example.com/lodash.js']],
            integrity: ['https://cdn.example.com/react.js' => 'sha384-abc']
        );

        $json = json_encode($map, JSON_PRETTY_PRINT);

        $expected = [
            'imports' => ['react' => 'https://cdn.example.com/react.js'],
            'scopes' => ['/scope/' => ['lodash' => 'https://cdn.example.com/lodash.js']],
            'integrity' => ['https://cdn.example.com/react.js' => 'sha384-abc'],
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), $json);
    }

    public function testCountable(): void
    {
        $map = new ImportMap(imports: ['a' => 'url-a', 'b' => 'url-b']);
        $this->assertCount(2, $map);
    }

    public function testIterator(): void
    {
        $map = new ImportMap(imports: ['a' => 'url-a', 'b' => 'url-b']);
        $this->assertSame(['a' => 'url-a', 'b' => 'url-b'], iterator_to_array($map));
    }

    public function testEmptySpecifierThrowsException(): void
    {
        $map = new ImportMap();

        $this->expectException(InvalidEntryException::class);
        $this->expectExceptionMessage('Specifier cannot be empty.');

        $map->add('', '/utils.js');
    }

    public function testTrailingSlashMismatchThrowsException(): void
    {
        $map = new ImportMap();

        $this->expectException(InvalidEntryException::class);
        $this->expectExceptionMessage("Specifier 'app/' ends with a slash, so the address '/js/app' must also end with a slash.");

        $map->add('app/', '/js/app');
    }

    public function testTrailingSlashMatchIsValid(): void
    {
        $map = new ImportMap();
        $map->add('app/', '/js/app/');

        $this->assertSame('/js/app/', $map->imports['app/']);
    }

    public function testFromJson(): void
    {
        $json = <<<'JSON'
        {
            "imports": {
                "react": "https://esm.sh/react@18.2.0"
            },
            "scopes": {
                "/admin/": {
                    "lodash": "https://unpkg.com/lodash-es@4.17.21/lodash.js"
                }
            },
            "integrity": {
                "https://esm.sh/react@18.2.0": "sha384-..."
            }
        }
        JSON;

        $map = ImportMap::fromJson($json);

        $this->assertSame(['react' => 'https://esm.sh/react@18.2.0'], $map->imports);
        $this->assertSame(['/admin/' => ['lodash' => 'https://unpkg.com/lodash-es@4.17.21/lodash.js']], $map->scopes);
        $this->assertSame(['https://esm.sh/react@18.2.0' => 'sha384-...'], $map->integrity);
    }

    public function testFromFile(): void
    {
        $file = sys_get_temp_dir().'/importmap.json';
        $json = '{"imports": {"vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"}}';
        file_put_contents($file, $json);

        try {
            $map = ImportMap::fromFile($file);
            $this->assertSame(['vue' => 'https://unpkg.com/vue@3/dist/vue.esm-browser.js'], $map->imports);
        } finally {
            unlink($file);
        }
    }

    public function testFromFileNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File not found');

        ImportMap::fromFile('/path/to/non-existent/file.json');
    }

    public function testFromFileNotReadable(): void
    {
        $file = sys_get_temp_dir().'/unreadable_importmap.json';
        touch($file);
        chmod($file, 0000);

        try {
            // Suppress warning from file_get_contents
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage('Could not read file');

            @ImportMap::fromFile($file);
        } finally {
            chmod($file, 0644);
            unlink($file);
        }
    }
}
