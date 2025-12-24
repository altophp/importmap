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

namespace Alto\ImportMap\Exception;

/**
 * Thrown when resolution fails.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
class ResolutionFailedException extends \RuntimeException implements ExceptionInterface
{
}
