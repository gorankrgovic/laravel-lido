<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateLoveLikeCountersTable.
 */
class CreateListenCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listen_counters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('listenable_id')->nullable();
            $table->uuid('listenable_type')->nullable();
            $table->enum('type_id', [
                'LISTEN',
                'DOWNLOAD',
            ])->default('LISTEN');
            $table->integer('count')->unsigned()->default(0);
            $table->timestamps();

            $table->unique([
                'listenable_id',
                'listenable_type',
                'type_id',
            ], 'listen_counter_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listen_counters');
    }
}
