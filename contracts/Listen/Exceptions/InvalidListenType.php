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


namespace Gox\Contracts\Lido\Listen\Exceptions;

use RuntimeException;

class InvalidListenType extends RuntimeException
{
    public static function notExists(string $type)
    {
        return new static("Listen type `{$type}` not exist.");
    }
}