# Rendering

`HtmlRenderer::render(ImportMap $map, bool $preload = true): string` produces the import-map script and, by default, module-preload links.

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Alto\ImportMap\ImportMap;
use Alto\ImportMap\Renderer\HtmlRenderer;

$map = new ImportMap();
$map
    ->add('app', '/assets/app.js', integrity: 'sha384-example')
    ->add('blocked', null)
    ->add('admin', '/assets/admin.js', scope: '/admin/');

$html = (new HtmlRenderer())->render($map, preload: true);
echo $html;
```

The JSON includes imports, scopes, null mappings, and integrity metadata. Encoding uses `JSON_THROW_ON_ERROR`, unescaped slashes, and the `JSON_HEX_*` flags so map content cannot close the script element.

Preload links are generated only for non-null top-level imports. Scoped entries are not preloaded, and integrity metadata is not added as an HTML `integrity` attribute. Disable every preload link with `preload: false` when the application needs a different strategy.

Addresses in preload links are escaped with `htmlspecialchars()` before insertion into the `href` attribute.

Custom renderers implement:

`RendererInterface::render(ImportMap $map, bool $preload = true): string`
