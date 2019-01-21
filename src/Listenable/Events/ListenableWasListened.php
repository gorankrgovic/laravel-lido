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


namespace Gox\Laravel\Lido\Listenable\Events;

use Gox\Contracts\Lido\Listenable\Models\Listenable as ListenableContract;

class ListenableWasListened
{

    /**
     * @var \Gox\Contracts\Lido\Listenable\Models\Listenable
     */
    public $listenable;

    /**
     * @var null
     */
    public $listenerId;

    /**
     * ListenableWasListened constructor.
     * @param \Gox\Contracts\Lido\Listenable\Models\Listenable $listenable
     * @param null $userId
     */
    public function __construct(ListenableContract $listenable, $userId = null)
    {
        $this->listenable = $listenable;
        $this->listenerId = $userId;
    }
}