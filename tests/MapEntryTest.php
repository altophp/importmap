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

use Alto\ImportMap\MapEntry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MapEntry::class)]
final class MapEntryTest extends TestCase
{
    public function testConstruct(): void
    {
        $entry = new MapEntry('react', 'https://cdn.example.com/react.js', 'sha384-abc');

        $this->assertSame('react', $entry->specifier);
        $this->assertSame('https://cdn.example.com/react.js', $entry->address);
        $this->assertSame('sha384-abc', $entry->integrity);
    }

    public function testConstructWithoutIntegrity(): void
    {
        $entry = new MapEntry('react', 'https://cdn.example.com/react.js');

        $this->assertNull($entry->integrity);
    }
}
