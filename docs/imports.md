# Imports

An `ImportMap` contains three public collections:

- `imports`: top-level `specifier => address` mappings;
- `scopes`: scope prefixes containing their own mappings;
- `integrity`: resolved addresses mapped to integrity metadata.

Addresses may be strings or `null`. A null exact mapping deliberately blocks
resolution.

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

echo json_encode($map, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), "\n";
```

The output is:

```text
{
    "imports": {
        "app": "\/assets\/app.js",
        "vendor\/": "\/assets\/vendor\/",
        "legacy": null
    },
    "scopes": {
        "\/admin\/": {
            "ui": "\/assets\/admin-ui.js"
        },
        "\/reports\/": {
            "charts": "\/assets\/charts.js"
        }
    },
    "integrity": {
        "\/assets\/app.js": "sha384-example"
    }
}
```

`add()` is a shortcut around `addEntry()`. Both methods mutate the current map
and return it for chaining.

An empty specifier throws `InvalidEntryException`. When a specifier ends with
`/`, its non-null address must also end with `/`; this keeps prefix resolution
well-defined. Integrity is stored by address, so it follows that exact resolved
address across top-level and scoped entries.

## Resolve imports

`NativeResolver` resolves an import in PHP and returns its address with nullable
integrity metadata.

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Alto\ImportMap\Resolver\NativeResolver;

$resolver = new NativeResolver();

$global = $resolver->resolve('app', $map);
$dependency = $resolver->resolve('vendor/router.js', $map);
$scoped = $resolver->resolve('ui', $map, '/admin/dashboard.js');

echo $global['url'].PHP_EOL;
echo $global['integrity'].PHP_EOL;
echo $dependency['url'].PHP_EOL;
echo $scoped['url'].PHP_EOL;
```

The output is:

```text
/assets/app.js
sha384-example
/assets/vendor/router.js
/assets/admin-ui.js
```

Resolution checks matching scopes from longest to shortest, then top-level
imports, then passes through absolute URLs and absolute or relative paths. An
exact match wins over a prefix, and the longest matching prefix wins. The PHP
resolver does not fetch a module, resolve a relative address against a browser
document, or prove that an address exists.
