<?php
/*
 * This file is part of Laravel Lido.
 *
 * (c) Goran Krgovic <gorankrgovic1@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);


namespace Gox\Contracts\Lido\Listenable\Exceptions;

use RuntimeException;

class InvalidListenable extends RuntimeException
{

    public static function notExists(string $type)
    {
        return new static("[$type] class or morph map not found.");
    }

    public static function notImplementInterface(string $type)
    {
        return new static("[{$type}] must implement `\Gox\Contracts\Lido\Listenable\Models\Listenable` contract.");
    }

}