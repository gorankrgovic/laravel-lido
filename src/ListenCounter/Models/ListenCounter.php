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


namespace Gox\Laravel\Lido\ListenCounter\Models;

use Gox\Contracts\Lido\ListenCounter\Models\ListenCounter as ListenCounterContract;
use Gox\Laravel\Lido\UuidTrait\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

class ListenCounter extends Model implements ListenCounterContract
{

    use GenerateUuid;

    /**
     * Since we are using the UUID as the ID we are not incrementing the model
     *
     * @var bool
     */
    public $incrementing = false;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'listen_counters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id',
        'count',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'count' => 'integer',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function listenable()
    {
        return $this->morphTo();
    }
}