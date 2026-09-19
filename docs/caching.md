# Caching

Alto Importmap does not include a cache layer. Cache the rendered output in the
application when rebuilding the same complete map is expensive. Derive the key
after every package map and application override has been merged.

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Alto\ImportMap\ImportMap;
use Alto\ImportMap\Renderer\HtmlRenderer;

$map = new ImportMap(imports: ['app' => '/assets/app.js']);
$state = json_encode($map, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
$key = 'importmap.'.hash('sha256', $state);

$cache = [];
$html = $cache[$key] ??= (new HtmlRenderer())->render($map);

echo $key.PHP_EOL;
echo $html;
```

The key changes when the serialized imports, scopes, integrity metadata, or
their order changes. If maps come from files, package manifests, deployment
metadata, or environment-specific URLs, include their resulting map state in
the key rather than relying only on a fixed cache name.

`ImportMap` is mutable. Calling `add()`, `addEntry()`, or `merge()` after output
has been cached does not invalidate application storage. Finish the map first,
then derive the key and render it. Cache import-map HTML and other page output
under separate keys when their preload settings differ.
