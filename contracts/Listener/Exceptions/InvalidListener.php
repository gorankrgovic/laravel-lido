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


namespace Gox\Contracts\Lido\Listener\Exceptions;

use RuntimeException;

class InvalidListener extends RuntimeException
{
    public static function notDefined()
    {
        return new static('Listener not defined.');
    }
}