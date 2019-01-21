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


namespace Gox\Laravel\Lido\Listen\Observers;

use Gox\Contracts\Lido\Listen\Models\Listen as ListenContract;
use Gox\Contracts\Lido\Listenable\Services\ListenableService as ListenableServiceContract;
use Gox\Laravel\Lido\Listen\Enums\ListenType;
use Gox\Laravel\Lido\Listenable\Events\ListenableWasDownloaded;
use Gox\Laravel\Lido\Listenable\Events\ListenableWasListened;

class ListenObserver
{
    /**
     * @param ListenContract $listen
     */
    public function created(ListenContract $listen)
    {
        if ($listen->type_id == ListenType::LISTEN) {
            event(new ListenableWasListened($listen->listenable, $listen->user_id));
            app(ListenableServiceContract::class)->incrementListensCount($listen->listenable);
        } else {
            event(new ListenableWasDownloaded($listen->listenable, $listen->user_id));
            app(ListenableServiceContract::class)->incrementDownloadsCount($listen->listenable);
        }
    }
}