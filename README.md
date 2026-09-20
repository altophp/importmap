# Alto ImportMap

Alto ImportMap is a PHP library to build, validate, and
render [import maps](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/script/type/importmap)
in PHP for web applications. It supports imports, scopes and integrity metadata, and includes a resolver aligned with
the WICG resolution algorithm.

## Installation

```bash
composer require alto/importmap
```

## Basic Usage

### 1. Create an ImportMap

You can build an `ImportMap` manually or by adding entries.

```php
use Alto\ImportMap\ImportMap;

$map = new ImportMap();

// Add a simple mapping
$map->add('react', 'https://esm.sh/react@18.2.0');
$map->add('react-dom', 'https://esm.sh/react-dom@18.2.0');
```

### 2. Load from File (Optional)

You can also load an existing import map from a JSON file.

```php
$map = ImportMap::fromFile(__DIR__ . '/importmap.json');

// You can still add entries dynamically
$map->add('app', '/js/app.js');
```

### 3. Render to HTML

Use the `HtmlRenderer` to generate the `<script type="importmap">` tag. It also supports `modulepreload` links for
performance.

```php
use Alto\ImportMap\Renderer\HtmlRenderer;

$renderer = new HtmlRenderer();

// Render the import map script and preload links
echo $renderer->render($map);
```

**Output:**

```html

<script type="importmap">
    {"imports":{"react":"https:\/\/esm.sh\/react@18.2.0","react-dom":"https:\/\/esm.sh\/react-dom@18.2.0"}}
</script>
<link rel="modulepreload" href="https://esm.sh/react@18.2.0">
<link rel="modulepreload" href="https://esm.sh/react-dom@18.2.0">
```

## Advanced Features

### Scoped Imports

You can define imports that only apply to specific scopes (paths).

```php
// Available globally
$map->add('lodash', 'https://unpkg.com/lodash-es@4.17.21/lodash.js');

// Different version for a specific scope
$map->add(
    'lodash', 
    'https://unpkg.com/lodash-es@3.10.1/lodash.js', 
    scope: '/legacy/'
);
```

### Integrity Hashes

Ensure security by adding integrity hashes (SRI) to your entries. The renderer includes these in the import map. The
`Resolver` can also use them, and you can access them via the `$map->integrity` property if needed for other tags.

```php
$map->add(
    'app', 
    '/js/app.js', 
    integrity: 'sha384-...'
);
```

### Merging Maps

You can combine multiple maps, which is useful for modular applications.

```php
$coreMap = new ImportMap(['react' => '...']);
$pluginMap = new ImportMap(['plugin-ui' => '...']);

$coreMap->merge($pluginMap);
```

### Resolving Imports

The `NativeResolver` implements
the [WICG Import Maps resolution algorithm](https://wicg.github.io/import-maps/#resolve-an-import-map-specifier).
This allows you to resolve a specifier to a URL within your PHP application (e.g., for bundling or server-side
rendering).

```php
use Alto\ImportMap\Resolver\NativeResolver;

$resolver = new NativeResolver();

try {
    $resolution = $resolver->resolve('react', $map);
    // ['url' => 'https://esm.sh/react@18.2.0', 'integrity' => null]
    
    echo $resolution['url']; 
} catch (ResolutionFailedException $e) {
    // Handle error
}
```

## Documentation

- [Installation](docs/installation.md): install the package and verify its requirements.
- [Getting started](docs/getting-started.md): build and render a first import map.
- [Imports](docs/imports.md): define and resolve exact, prefix, and scoped imports.
- [Packages](docs/packages.md): load and combine maps contributed by packages.
- [Output](docs/output.md): generate safe import-map and module-preload tags.
- [Caching](docs/caching.md): cache generated output without hiding map changes.
- [Errors](docs/errors.md): recover from invalid entries, files, and resolutions.
- [Complete documentation](docs/index.md): review the package scope and every guide.

## Contributing

Contributions of all kinds are welcome. Visit the
[project on GitHub](https://github.com/altophp/importmap) to
[report a bug](https://github.com/altophp/importmap/issues/new),
[suggest a feature](https://github.com/altophp/importmap/issues/new), or
[open a pull request](https://github.com/altophp/importmap/pulls).

Before submitting code, run:

```bash
# Runs PHP CS Fixer, PHPStan, and PHPUnit
composer qa
```

Changes to public behavior should include tests and documentation.

## Support

ALTO ImportMap is open source and independently maintained by
[Simon André](https://smnandre.dev). If it is useful to your work, you can
support its continued development through
[GitHub Sponsors](https://github.com/sponsors/smnandre).

Sharing the package or
[starring it on GitHub](https://github.com/altophp/importmap) also helps.

## License

ALTO ImportMap is released by [ALTO PHP](https://altophp.com) under the
[MIT License](LICENSE).
