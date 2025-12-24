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

namespace Alto\ImportMap;

/**
 * Represents a single entry in the import map.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class MapEntry
{
    public function __construct(
        public string $specifier,
        public ?string $address,
        public ?string $integrity = null,
    ) {
    }
}
