# Getting started

Build an import map, then render the HTML tags needed by the browser.

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Alto\ImportMap\ImportMap;
use Alto\ImportMap\Renderer\HtmlRenderer;

$map = new ImportMap();
$map
    ->add('app', '/assets/app.js')
    ->add('vendor/', '/assets/vendor/');

echo (new HtmlRenderer())->render($map);
```

The renderer produces an import-map script followed by preload links for top-level, non-null imports:

```html
<script type="importmap">{"imports":{"app":"/assets/app.js","vendor/":"/assets/vendor/"}}</script>
<link rel="modulepreload" href="/assets/app.js">
<link rel="modulepreload" href="/assets/vendor/">
```

`ImportMap` is mutable: `add()`, `addEntry()`, and `merge()` update the current map and return it for chaining.

Next, define and resolve [imports](imports.md), combine [package maps](packages.md),
or configure browser [output](output.md).
