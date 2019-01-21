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


namespace Gox\Contracts\Lido\Listenable\Services;

use Gox\Contracts\Lido\Listenable\Models\Listenable as ListenableContract;

interface ListenableService
{
    public function addListenTo(ListenableContract $listenable, $type, $userId);

    public function isListenedOrDownloaded(ListenableContract $listenable, $type, $userId): bool;

    public function incrementListensCount(ListenableContract $listenable);

    public function incrementDownloadsCount(ListenableContract $listenable);

    public function removeListenCountersOfType($listenableType, $type = null);

    public function removeModelListens(ListenableContract $listenable, $type);

    public function collectListenersOf(ListenableContract $listenable);

    public function collectDownloadersOf(ListenableContract $listenable);

    public function fetchListenersCounters($listenableType, $listenType): array;

}