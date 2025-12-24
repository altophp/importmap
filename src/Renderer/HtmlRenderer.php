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

namespace Alto\ImportMap\Renderer;

use Alto\ImportMap\ImportMap;

/**
 * Renders the Import Map as HTML tags.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class HtmlRenderer implements RendererInterface
{
    /**
     * Renders the map to an HTML string.
     */
    public function render(ImportMap $map, bool $preload = true): string
    {
        $json = json_encode($map, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        $output = sprintf('<script type="importmap">%s</script>', $json);

        if ($preload) {
            foreach ($map->imports as $url) {
                if (null === $url) {
                    continue;
                }

                $output .= sprintf("\n<link rel=\"modulepreload\" href=\"%s\">", htmlspecialchars($url, ENT_QUOTES));
            }
        }

        return $output;
    }
}
