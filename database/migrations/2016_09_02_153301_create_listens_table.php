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
 * Class CreateLoveLikesTable.
 */
class CreateListensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('listenable_id')->nullable();
            $table->uuid('listenable_type')->nullable();
            $table->uuid('user_id')->nullable()->index();
            $table->enum('type_id', [
                'LISTEN',
                'DOWNLOAD',
            ])->default('LISTEN');
            $table->timestamps();

            $table->unique([
                'listenable_type',
                'listenable_id',
                'user_id',
            ], 'listen_user_unique');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listens');
    }
}
