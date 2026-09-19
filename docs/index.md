# Alto Importmap

Alto Importmap builds, combines, renders, and resolves JavaScript import maps
in PHP. It supports exact and prefix imports, scopes, integrity metadata,
blocked entries, and module preloads.

```php
use Alto\ImportMap\ImportMap;

$map = new ImportMap();
$map->add('app', '/assets/app.js');
```

## Documentation

- [Installation](installation.md): install the package and verify its requirements.
- [Getting started](getting-started.md): build and render a first import map.
- [Imports](imports.md): define and resolve exact, prefix, and scoped imports.
- [Packages](packages.md): load and combine maps contributed by packages.
- [Output](output.md): generate safe import-map and module-preload tags.
- [Caching](caching.md): cache generated output without hiding map changes.
- [Errors](errors.md): recover from invalid entries, files, and resolutions.

The package represents import maps and returns HTML or resolution data. It does
not download JavaScript modules, verify remote content, or write configuration
files.
