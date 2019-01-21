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


namespace Gox\Contracts\Lido\Listenable\Models;

/**
 * Interface Listenable
 * @package Gox\Contracts\Lido\Listenable\Models
 */
interface Listenable
{

    public function getKey();

    public function getMorphClass();

    public function listensAndDownloads();

    public function listens();

    public function downloads();

    public function listensCounter();

    public function downloadsCounter();

    public function collectListeners();

    public function collectDownloaders();

    public function listenBy($userId = null);

    public function downloadBy($userId = null);

    public function removeListens();

    public function removeDownloads();

    public function isListenedBy($userId = null): bool;

    public function isDownloadedBy($userId = null): bool;
}