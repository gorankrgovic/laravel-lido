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


namespace Gox\Contracts\Lido\Listener\Models;

use Gox\Contracts\Lido\Listenable\Models\Listenable;

/**
 * Interface Listener
 * @package Gox\Contracts\Lido\Listener\Models
 */
interface Listener
{

    public function listen(Listenable $listenable);

    public function download(Listenable $listenable);

    public function hasListened(Listenable $listenable);

    public function hasDownloaded(Listenable $listenable): bool;

}