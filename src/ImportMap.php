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

use Alto\ImportMap\Exception\InvalidEntryException;

/**
 * The aggregate root representing an Import Map.
 *
 * @implements \IteratorAggregate<string, ?string>
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class ImportMap implements \JsonSerializable, \Countable, \IteratorAggregate
{
    /**
     * @param array<string, ?string>                $imports
     * @param array<string, array<string, ?string>> $scopes
     * @param array<string, string>                 $integrity
     */
    public function __construct(
        public array $imports = [],
        public array $scopes = [],
        public array $integrity = [],
    ) {
    }

    /**
     * Creates an ImportMap from a JSON string.
     *
     * @throws \JsonException
     */
    public static function fromJson(string $json): self
    {
        /** @var array{imports?: array<string, ?string>, scopes?: array<string, array<string, ?string>>, integrity?: array<string, string>} $data */
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return new self(
            imports: $data['imports'] ?? [],
            scopes: $data['scopes'] ?? [],
            integrity: $data['integrity'] ?? [],
        );
    }

    /**
     * Creates an ImportMap from a file.
     *
     * @throws \RuntimeException If the file cannot be read
     * @throws \JsonException    If the JSON is invalid
     */
    public static function fromFile(string $path): self
    {
        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('File not found: %s', $path));
        }

        $content = file_get_contents($path);
        if (false === $content) {
            throw new \RuntimeException(sprintf('Could not read file: %s', $path));
        }

        return self::fromJson($content);
    }

    /**
     * Adds a mapping to the map.
     */
    public function add(string $specifier, ?string $address, ?string $integrity = null, ?string $scope = null): self
    {
        return $this->addEntry(new MapEntry($specifier, $address, $integrity), $scope);
    }

    /**
     * Adds a MapEntry to the map.
     */
    public function addEntry(MapEntry $entry, ?string $scope = null): self
    {
        if ('' === $entry->specifier) {
            throw new InvalidEntryException('Specifier cannot be empty.');
        }

        if (null !== $entry->address && str_ends_with($entry->specifier, '/') && !str_ends_with($entry->address, '/')) {
            throw new InvalidEntryException(sprintf("Specifier '%s' ends with a slash, so the address '%s' must also end with a slash.", $entry->specifier, $entry->address));
        }

        if ($scope) {
            $this->scopes[$scope][$entry->specifier] = $entry->address;
        } else {
            $this->imports[$entry->specifier] = $entry->address;
        }

        if ($entry->integrity && $entry->address) {
            $this->integrity[$entry->address] = $entry->integrity;
        }

        return $this;
    }

    /**
     * Merges another map into this one.
     */
    public function merge(self $other): self
    {
        $this->imports = array_merge($this->imports, $other->imports);
        $this->scopes = array_merge($this->scopes, $other->scopes);
        $this->integrity = array_merge($this->integrity, $other->integrity);

        return $this;
    }

    /**
     * Returns the number of top-level imports.
     */
    public function count(): int
    {
        return count($this->imports);
    }

    /**
     * Returns an iterator for top-level imports.
     *
     * @return \Traversable<string, ?string>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->imports);
    }

    /**
     * Serializes the map to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'imports' => $this->imports,
            'scopes' => array_filter($this->scopes),
            'integrity' => $this->integrity,
        ]);
    }
}
