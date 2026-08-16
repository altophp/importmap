# Maps

An `ImportMap` contains three public collections:

- `imports`: top-level `specifier => address` mappings.
- `scopes`: scope prefixes containing their own mappings.
- `integrity`: resolved addresses mapped to integrity metadata.

Addresses may be strings or `null`. A null exact mapping explicitly blocks resolution.

## Add entries

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Alto\ImportMap\ImportMap;
use Alto\ImportMap\MapEntry;

$map = new ImportMap();
$map
    ->add('app', '/assets/app.js', integrity: 'sha384-example')
    ->add('vendor/', '/assets/vendor/')
    ->add('legacy', null)
    ->add('ui', '/assets/admin-ui.js', scope: '/admin/');

$map->addEntry(
    new MapEntry('charts', '/assets/charts.js'),
    scope: '/reports/',
);

echo json_encode($map, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
```

`add(string $specifier, ?string $address, ?string $integrity = null, ?string $scope = null)` is a shortcut around `addEntry(MapEntry $entry, ?string $scope = null)`.

An empty specifier throws `InvalidEntryException`. When a specifier ends with `/`, its non-null address must also end with `/`; this keeps prefix resolution well-defined.

Integrity is stored by address, not by specifier. Adding an integrity value to a scoped or top-level entry therefore makes it available whenever that exact address is resolved.

## Construct or load a map

The constructor accepts the three collections directly:

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Alto\ImportMap\ImportMap;

$map = new ImportMap(
    imports: ['app' => '/assets/app.js'],
    scopes: ['/admin/' => ['app' => '/assets/admin.js']],
    integrity: ['/assets/app.js' => 'sha384-example'],
);

$fromJson = ImportMap::fromJson(
    '{"imports":{"editor":"/assets/editor.js"}}',
);

echo count($map).' top-level import(s)'.PHP_EOL;
foreach ($fromJson as $specifier => $address) {
    echo $specifier.' -> '.$address.PHP_EOL;
}
```

`fromJson()` throws `JsonException` for invalid JSON. `fromFile()` reads the same structure and throws `RuntimeException` when the path does not exist or cannot be read.

`count()` and iteration cover top-level imports only. `json_encode()` omits empty sections, except that an entirely empty map currently serializes as `[]`.

## Merge maps

`merge(ImportMap $other): self` mutates the receiving map. Entries from the other map replace entries with the same key.

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Alto\ImportMap\ImportMap;

$application = new ImportMap(imports: ['app' => '/assets/app.js']);
$feature = new ImportMap(imports: ['charts' => '/assets/charts.js']);

$application->merge($feature);

echo json_encode($application, JSON_THROW_ON_ERROR);
```

Top-level imports and integrity entries merge individually. A scope from the other map replaces the complete scope with the same prefix; merge the entries yourself first when two maps must contribute to one scope.
