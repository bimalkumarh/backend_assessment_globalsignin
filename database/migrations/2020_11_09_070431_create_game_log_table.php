<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_logs', function (Blueprint $table) {
            $table->id();
            $table->string('game_id');
            $table->string('user_id');
            $table->string('playerOneName');
            $table->string('playerTwoName');
            $table->json('recorder');
            $table->string('playerOneLifeValue');
            $table->string('playerTwoLifeValue');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_logs', function (Blueprint $table) {
            Schema::dropIfExists('game_logs');
        });
    }
}
