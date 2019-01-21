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


namespace Gox\Laravel\Lido\Listener\Models\Traits;

use Gox\Contracts\Lido\Listenable\Models\Listenable as ListenableContract;
use Gox\Contracts\Lido\Listenable\Services\ListenableService as ListenableServiceContract;
use Gox\Laravel\Lido\Listen\Enums\ListenType;

trait Listener
{

    public function listen(ListenableContract $listenable)
    {
        app(ListenableServiceContract::class)->addListenTo($listenable, ListenType::LISTEN, $this);
    }

    public function download(ListenableContract $listenable)
    {
        app(ListenableServiceContract::class)->addListenTo($listenable, ListenType::LISTEN, $this);
    }

    public function hasListened(ListenableContract $listenable): bool
    {
        return app(ListenableServiceContract::class)->isListenedOrDownloaded($listenable, ListenType::LISTEN, $this);
    }

    public function hasDownloaded(ListenableContract $listenable): bool
    {
        return app(ListenableServiceContract::class)->isListenedOrDownloaded($listenable, ListenType::DOWNLOAD, $this);
    }
}