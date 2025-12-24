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
use Alto\ImportMap\Exception\SpecifierNotFoundException;
use Alto\ImportMap\ImportMap;

/**
 * Resolves specifiers using the WICG Import Maps algorithm.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final readonly class NativeResolver implements ResolverInterface
{
    /**
     * Resolves a specifier to a URL.
     *
     * @return array{url: string, integrity: ?string}
     */
    #[\Override]
    public function resolve(string $specifier, ImportMap $map, ?string $referencingUrl = null): array
    {
        // 1. Scope Resolution
        if ($referencingUrl && !empty($map->scopes)) {
            $matchedScopes = [];
            foreach ($map->scopes as $scopePrefix => $scopeImports) {
                // Scope Match: The referencing script URL must start with the scope prefix
                if (str_starts_with($referencingUrl, $scopePrefix)) {
                    $matchedScopes[] = $scopePrefix;
                }
            }

            // Sort scopes by length (longest match first)
            usort($matchedScopes, fn (string $a, string $b): int => strlen($b) <=> strlen($a));

            foreach ($matchedScopes as $scopePrefix) {
                $scopeImports = $map->scopes[$scopePrefix];
                $url = $this->resolveImportMatch($specifier, $scopeImports);
                if (null !== $url) {
                    return $this->prepareResult($url, $map);
                }
            }
        }

        // 2. Top-level Imports
        $url = $this->resolveImportMatch($specifier, $map->imports);

        if (null !== $url) {
            return $this->prepareResult($url, $map);
        }

        // 3. URL passthrough
        if (parse_url($specifier, PHP_URL_SCHEME) || str_starts_with($specifier, '/') || str_starts_with($specifier, './') || str_starts_with($specifier, '../')) {
            return $this->prepareResult($specifier, $map);
        }

        // 4. Failure
        throw new SpecifierNotFoundException($specifier);
    }

    /**
     * @param array<string, ?string> $imports
     */
    private function resolveImportMatch(string $specifier, array $imports): ?string
    {
        // 1. Direct Match
        if (array_key_exists($specifier, $imports)) {
            $address = $imports[$specifier];

            if (null === $address) {
                throw new ResolutionFailedException(sprintf("Resolution blocked by null entry for '%s'.", $specifier));
            }

            return $address;
        }

        // 2. Prefix Match
        $candidates = array_filter(
            array_keys($imports),
            fn (string $k): bool => str_ends_with($k, '/') && str_starts_with($specifier, $k)
        );

        if ($candidates) {
            usort($candidates, fn (string $a, string $b): int => strlen($b) <=> strlen($a));
            $bestMatch = $candidates[0];

            return $imports[$bestMatch].substr($specifier, strlen($bestMatch));
        }

        return null;
    }

    /**
     * @return array{url: string, integrity: ?string}
     */
    private function prepareResult(string $url, ImportMap $map): array
    {
        $hash = $map->integrity[$url] ?? null;

        return ['url' => $url, 'integrity' => $hash];
    }
}
