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


namespace Gox\Laravel\Lido\Listen\Models;

use Gox\Contracts\Lido\Listen\Models\Listen as ListenContract;
use Gox\Laravel\Lido\UuidTrait\GenerateUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Listen
 * @package Gox\Laravel\Lido\Listen\Models
 */
class Listen extends Model implements ListenContract
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
    protected $table = 'listens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type_id',
    ];

    /**
     * Listenable model relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function listenable()
    {
        return $this->morphTo();
    }
}