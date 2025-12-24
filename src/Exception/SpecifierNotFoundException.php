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

namespace Alto\ImportMap\Exception;

/**
 * Thrown when a specific specifier cannot be found in the map.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class SpecifierNotFoundException extends ResolutionFailedException
{
    public function __construct(string $specifier)
    {
        parent::__construct(sprintf("The specifier '%s' could not be resolved.", $specifier));
    }
}
