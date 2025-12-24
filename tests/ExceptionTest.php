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
use Alto\ImportMap\Exception\ResolutionFailedException;
use Alto\ImportMap\Exception\SpecifierNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidEntryException::class)]
#[CoversClass(ResolutionFailedException::class)]
#[CoversClass(SpecifierNotFoundException::class)]
final class ExceptionTest extends TestCase
{
    public function testSpecifierNotFoundExceptionMessage(): void
    {
        $exception = new SpecifierNotFoundException('missing-package');
        $this->assertSame("The specifier 'missing-package' could not be resolved.", $exception->getMessage());
    }

    public function testInvalidEntryException(): void
    {
        $exception = new InvalidEntryException('Invalid entry');
        $this->assertSame('Invalid entry', $exception->getMessage());
    }

    public function testResolutionFailedException(): void
    {
        $exception = new ResolutionFailedException('Resolution failed');
        $this->assertSame('Resolution failed', $exception->getMessage());
    }
}
