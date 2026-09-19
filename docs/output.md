# Output

`HtmlRenderer::render(ImportMap $map, bool $preload = true): string` produces an
import-map script and, by default, module-preload links.

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

The output is:

```html
<script type="importmap">{"imports":{"app":"/assets/app.js","blocked":null},"scopes":{"/admin/":{"admin":"/assets/admin.js"}},"integrity":{"/assets/app.js":"sha384-example"}}</script>
<link rel="modulepreload" href="/assets/app.js">
```

The JSON includes imports, scopes, null mappings, and integrity metadata. The
renderer uses the `JSON_HEX_*` flags so map content cannot close the script
element, and escapes preload addresses for the `href` attribute.

Preload links are generated only for non-null top-level imports. Scoped entries
are not preloaded, and integrity metadata is not copied to an HTML `integrity`
attribute. Pass `preload: false` when the application uses another preload
strategy.

Insert the generated map before scripts that import its specifiers. A mapping
does not publish or download its target, and the package does not validate an
integrity hash against remote content. Custom renderers implement
`RendererInterface::render(ImportMap $map, bool $preload = true): string`.
