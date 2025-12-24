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
 * Interface for Import Map renderers.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
interface RendererInterface
{
    /**
     * Renders the map to a string.
     */
    public function render(ImportMap $map, bool $preload = true): string;
}
