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


namespace Gox\Laravel\Lido\Listenable\Observers;

use Gox\Contracts\Lido\Listenable\Models\Listenable as ListenableContract;

/**
 * Class ListenableObserver
 * @package Gox\Laravel\Lido\Listenable\Observers
 */
class ListenableObserver
{

    /**
     * Handle on delete
     *
     * @param ListenableContract $listenable
     */
    public function deleted(ListenableContract $listenable)
    {
        if (!$this->removeListensOnDelete($listenable)) {
            return;
        }

        $listenable->removeListens();
    }

    /**
     * @param ListenableContract $listenable
     * @return bool
     */
    private function removeListensOnDelete(ListenableContract $listenable): bool
    {
        return $listenable->removeListensOnDelete ?? true;
    }
}