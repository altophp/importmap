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

namespace Alto\ImportMap\Resolver;

use Alto\ImportMap\Exception\ResolutionFailedException;
use Alto\ImportMap\ImportMap;

/**
 * Interface for specifier resolvers.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
interface ResolverInterface
{
    /**
     * Resolves a specifier to a URL and optional integrity hash.
     *
     * @param string|null $referencingUrl The URL of the script importing the specifier (required for scope resolution)
     *
     * @return array{url: string, integrity: ?string}
     *
     * @throws ResolutionFailedException
     */
    public function resolve(string $specifier, ImportMap $map, ?string $referencingUrl = null): array;
}
