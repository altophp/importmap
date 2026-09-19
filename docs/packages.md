# Packages

Applications can build one map per package or feature, then merge those maps
into the map rendered for the page. A map can also start from JSON supplied by a
package manifest or configuration file.

## Load a map

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Alto\ImportMap\ImportMap;

$package = ImportMap::fromJson(
    '{"imports":{"editor":"/assets/editor.js"}}',
);

echo count($package).' top-level import(s)'.PHP_EOL;
foreach ($package as $specifier => $address) {
    echo $specifier.' -> '.$address.PHP_EOL;
}
```

The output is:

```text
1 top-level import(s)
editor -> /assets/editor.js
```

`fromFile($path)` reads the same JSON structure. `count()` and iteration cover
top-level imports only; scopes and integrity remain available through their
public collections.

## Combine packages

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Alto\ImportMap\ImportMap;

$application = new ImportMap(imports: ['app' => '/assets/app.js']);
$feature = new ImportMap(imports: ['charts' => '/assets/charts.js']);

$application->merge($feature);
echo json_encode($application, JSON_THROW_ON_ERROR), "\n";
```

The output is:

```text
{"imports":{"app":"\/assets\/app.js","charts":"\/assets\/charts.js"}}
```

`merge()` mutates the receiving map. Top-level imports and integrity entries
merge by key, with the incoming value winning. An incoming scope replaces the
complete scope with the same prefix; merge its entries first when several
packages must contribute to one scope.
