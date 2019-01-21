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


namespace Gox\Laravel\Lido\Listenable\Models\Traits;

use Gox\Contracts\Lido\Listen\Models\Listen as ListenContract;
use Gox\Contracts\Lido\ListenCounter\Models\ListenCounter as ListenCounterContract;
use Gox\Contracts\Lido\Listenable\Services\ListenableService as ListenableServiceContract;
use Gox\Laravel\Lido\Listen\Enums\ListenType;
use Gox\Laravel\Lido\Listenable\Observers\ListenableObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

trait Listenable
{
    /**
     * Observe
     */
    public static function bootListenable()
    {
        static::observe(ListenableObserver::class);
    }

    /**
     * @return mixed
     */
    public function listensAndDownloads()
    {
        return $this->morphMany(app(ListenContract::class), 'listenable');
    }

    /**
     * @return mixed
     */
    public function listens()
    {
        return $this->listensAndDownloads()->where('type_id', ListenType::LISTEN);
    }

    /**
     * @return mixed
     */
    public function downloads()
    {
        return $this->listensAndDownloads()->where('type_id', ListenType::DOWNLOAD);
    }

    public function listensCounter()
    {
        return $this->morphOne(app(ListenCounterContract::class), 'listenable')
            ->where('type_id', ListenType::LISTEN);
    }

    public function downloadsCounter()
    {
        return $this->morphOne(app(ListenCounterContract::class), 'listenable')
            ->where('type_id', ListenType::DOWNLOAD);
    }

    public function collectListeners()
    {
        return app(ListenableServiceContract::class)->collectListenersOf($this);
    }

    public function collectDownloaders()
    {
        return app(ListenableServiceContract::class)->collectDownloadersOf($this);
    }

    public function getListensCountAttribute(): int
    {
        return $this->listensCounter ? $this->listensCounter->count : 0;
    }


    public function getDownloadsCountAttribute(): int
    {
        return $this->downloadsCounter ? $this->downloadsCounter->count : 0;
    }


    public function listenBy($userId = null)
    {
        app(ListenableServiceContract::class)->addListenTo($this, ListenType::LISTEN, $userId);
    }

    public function downloadBy($userId = null)
    {
        app(ListenableServiceContract::class)->addListenTo($this, ListenType::DOWNLOAD, $userId);
    }

    public function removeDownloads()
    {
        app(ListenableServiceContract::class)->removeModelListens($this, ListenType::DOWNLOAD);
    }

    public function removeListens()
    {
        app(ListenableServiceContract::class)->removeModelListens($this, ListenType::LISTEN);
    }

    /**
     * @param null $userId
     * @return bool
     */
    public function isListenedBy($userId = null): bool
    {
        return app(ListenableServiceContract::class)->isListenedOrDownloaded($this, ListenType::LISTEN, $userId);
    }


    /**
     * @param null $userId
     * @return bool
     */
    public function isDownloadedBy($userId = null): bool
    {
        return app(ListenableServiceContract::class)->isListenedOrDownloaded($this, ListenType::DOWNLOAD, $userId);
    }

    /**
     * @param Builder $query
     * @param null $userId
     * @return Builder
     */
    public function scopeWhereListenedBy(Builder $query, $userId = null): Builder
    {
        return $this->applyScopeWhereListenedBy($query, ListenType::LISTEN, $userId);
    }


    /**
     * @param Builder $query
     * @param null $userId
     * @return Builder
     */
    public function scopeWhereDownloadedBy(Builder $query, $userId = null): Builder
    {
        return $this->applyScopeWhereListenedBy($query, ListenType::DOWNLOAD, $userId);
    }

    /**
     * @param Builder $query
     * @param string $direction
     * @return Builder
     */
    public function scopeOrderByListensCount(Builder $query, string $direction = 'desc'): Builder
    {
        return $this->applyScopeOrderByListensCount($query, ListenType::LISTEN, $direction);
    }


    /**
     * @param Builder $query
     * @param string $direction
     * @return Builder
     */
    public function scopeOrderByDownloadsCount(Builder $query, string $direction = 'desc'): Builder
    {
        return $this->applyScopeOrderByListensCount($query, ListenType::DOWNLOAD, $direction);
    }


    /**
     * @param Builder $query
     * @param string $type
     * @param $userId
     * @return Builder
     */
    private function applyScopeWhereListenedBy(Builder $query, string $type, $userId): Builder
    {
        $service = app(ListenableServiceContract::class);
        $userId = $service->getListenerUserId($userId);
        $typeId = $service->getListenTypeId($type);

        return $query->whereHas('listensAndDownloads', function (Builder $innerQuery) use ($typeId, $userId) {
            $innerQuery->where('user_id', $userId);
            $innerQuery->where('type_id', $typeId);
        });
    }


    /**
     * @param Builder $query
     * @param string $listenType
     * @param string $direction
     * @return Builder
     */
    private function applyScopeOrderByListensCount(Builder $query, string $listenType, string $direction): Builder
    {
        $listenable = $query->getModel();
        $typeId = app(ListenableServiceContract::class)->getListenTypeId($listenType);

        return $query
            ->select($listenable->getTable() . '.*', 'listen_counters.count')
            ->leftJoin('listen_counters', function (JoinClause $join) use ($listenable, $typeId) {
                $join
                    ->on('listen_counters.listenable_id', '=', "{$listenable->getTable()}.{$listenable->getKeyName()}")
                    ->where('listen_counters.listenable_type', '=', $listenable->getMorphClass())
                    ->where('listen_counters.type_id', '=', $typeId);
            })
            ->orderBy('listen_counters.count', $direction);
    }

}