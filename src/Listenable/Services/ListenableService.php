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


namespace Gox\Laravel\Lido\Listenable\Services;

use Gox\Contracts\Lido\Listen\Exceptions\InvalidListenType;
use Gox\Contracts\Lido\Listen\Models\Listen as ListenContract;
use Gox\Contracts\Lido\Listenable\Models\Listenable as ListenableContract;
use Gox\Contracts\Lido\ListenCounter\Models\ListenCounter as ListenCounterContract;
use Gox\Contracts\Lido\Listener\Exceptions\InvalidListener;
use Gox\Contracts\Lido\Listenable\Services\ListenableService as ListenableServiceContract;
use Gox\Contracts\Lido\Listener\Models\Listener as ListenerContract;
use Gox\Laravel\Lido\Listen\Enums\ListenType;
use Illuminate\Support\Facades\DB;

class ListenableService implements ListenableServiceContract
{

    /**
     * @param ListenableContract $listenable
     * @param $type
     * @param $userId
     */
    public function addListenTo(ListenableContract $listenable, $type, $userId)
    {
        $userId = $this->getListenerUserId($userId);

        $listen = $listenable->listensAndDownloads()->where([
            'user_id' => $userId,
        ])->first();

        if (!$listen) {
            $listenable->listens()->create([
                'user_id' => $userId,
                'type_id' => $this->getListenTypeId($type),
            ]);

            return;
        }

//        if ($listen->type_id == $this->getListenTypeId($type)) {
//            return;
//        }

        $listen->delete();

        $listenable->listens()->create([
            'user_id' => $userId,
            'type_id' => $this->getListenTypeId($type),
        ]);
    }

    /**
     * @param ListenableContract $listenable
     */
    public function incrementDownloadsCount(ListenableContract $listenable)
    {
        $counter = $listenable->listensCounter()->first();

        if (!$counter) {
            $counter = $listenable->listensCounter()->create([
                'count' => 0,
                'type_id' => ListenType::DOWNLOAD,
            ]);
        }
        $counter->increment('count');
    }


    /**
     * @param ListenableContract $listenable
     */
    public function incrementListensCount(ListenableContract $listenable)
    {
        $counter = $listenable->listensCounter()->first();

        if (!$counter) {
            $counter = $listenable->listensCounter()->create([
                'count' => 0,
                'type_id' => ListenType::LISTEN,
            ]);
        }
        $counter->increment('count');
    }


    /**
     * @param ListenableContract $listenable
     * @param $type
     * @param $userId
     * @return bool
     */
    public function isListenedOrDownloaded(ListenableContract $listenable, $type, $userId): bool
    {
        if ($userId instanceof ListenerContract) {
            $userId = $userId->getKey();
        }

        if (is_null($userId)) {
            $userId = false;
        }

        if (!$userId) {
            return false;
        }

        $typeId = $this->getListenTypeId($type);

        $exists = $this->hasListenOrDownloadInLoadedRelation($listenable, $typeId, $userId);
        if (!is_null($exists)) {
            return $exists;
        }

        return $listenable->listensAndDownloads()->where([
            'user_id' => $userId,
            'type_id' => $typeId,
        ])->exists();
    }

    /**
     * @param $listenableType
     * @param null $type
     */
    public function removeListenCountersOfType($listenableType, $type = null)
    {
        if (class_exists($listenableType)) {
            /** @var \Gox\Contracts\Lido\Listenable\Models\Listenable $listenable */
            $listenable = new $listenableType;
            $listenableType = $listenable->getMorphClass();
        }

        /** @var \Illuminate\Database\Eloquent\Builder $counters */
        $counters = app(ListenCounterContract::class)->where('listenable_type', $listenableType);
        if (!is_null($type)) {
            $counters->where('type_id', $this->getListenTypeId($type));
        }
        $counters->delete();
    }

    /**
     * @param ListenableContract $listenable
     * @param $type
     */
    public function removeModelListens(ListenableContract $listenable, $type)
    {
        app(ListenContract::class)->where([
            'listenable_id' => $listenable->getKey(),
            'listenable_type' => $listenable->getMorphClass(),
            'type_id' => $this->getListenTypeId($type),
        ])->delete();

        app(ListenCounterContract::class)->where([
            'listenable_id' => $listenable->getKey(),
            'listenable_type' => $listenable->getMorphClass(),
            'type_id' => $this->getListenTypeId($type),
        ])->delete();
    }


    /**
     * @param ListenableContract $listenable
     * @return mixed
     */
    public function collectDownloadersOf(ListenableContract $listenable)
    {
        $userModel = $this->resolveUserModel();
        $listenersIds = $listenable->downloads->pluck('user_id');
        return $userModel::whereKey($listenersIds)->get();
    }

    /**
     * @param ListenableContract $listenable
     * @return mixed
     */
    public function collectListenersOf(ListenableContract $listenable)
    {
        $userModel = $this->resolveUserModel();
        $listenersIds = $listenable->listens->pluck('user_id');
        return $userModel::whereKey($listenersIds)->get();
    }

    /**
     * @param $listenableType
     * @param $listenType
     * @return array
     */
    public function fetchListenersCounters($listenableType, $listenType): array
    {
        /** @var \Illuminate\Database\Eloquent\Builder $listensCount */
        $listensCount = app(ListenContract::class)
            ->select([
                DB::raw('COUNT(*) AS count'),
                'listenable_type',
                'listenable_id',
                'type_id',
            ])
            ->where('listenable_type', $listenableType);

        if (!is_null($listenType)) {
            $listensCount->where('type_id', $this->getListenTypeId($listenType));
        }

        $listensCount->groupBy('listenable_id', 'type_id');

        return $listensCount->get()->toArray();
    }

    /**
     * @param $userId
     * @return |null
     */
    public function getListenerUserId($userId)
    {
        if ($userId instanceof ListenerContract) {
            return $userId->getKey();
        }

        if (is_null($userId)) {
            return null;
        }
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getListenTypeId($type)
    {
        $type = strtoupper($type);
        if (!defined("\\Gox\\Laravel\\Lido\\Listen\\Enums\\ListenType::{$type}")) {
            throw InvalidListenType::notExists($type);
        }

        return constant("\\Gox\\Laravel\\Lido\\Listen\\Enums\\ListenType::{$type}");
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    private function resolveUserModel()
    {
        return config('auth.providers.users.model');
    }

    /**
     * @param ListenableContract $listenable
     * @param $typeId
     * @param $userId
     * @return null|bool
     */
    private function hasListenOrDownloadInLoadedRelation(ListenableContract $listenable, $typeId, $userId)
    {
        $relations = $this->listenTypeRelations($typeId);

        foreach ($relations as $relation) {
            if (!$listenable->relationLoaded($relation)) {
                continue;
            }

            return $listenable->{$relation}->contains(function ($item) use ($userId, $typeId) {
                return $item->user_id == $userId && $item->type_id === $typeId;
            });
        }

        return null;
    }


    /**
     * Resolve list of relations by type.
     *
     * @param $type
     * @return mixed
     */
    private function listenTypeRelations($type)
    {
        $relations = [
            ListenType::LISTEN => [
                'listens',
                'listensAndDownloads',
            ],
            ListenType::DOWNLOAD => [
                'downloads',
                'listensAndDownloads',
            ],
        ];

        if (!isset($relations[$type])) {
            throw InvalidListenType::notExists($type);
        }
        return $relations[$type];
    }
}