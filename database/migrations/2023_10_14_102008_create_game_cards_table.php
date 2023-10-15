<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('game_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games');
            $table->foreignId('game_player_id')->nullable()->constrained('game_players');
            $table->string('card_rank');
            $table->string('card_suit');
            $table->string('card_type')->default('deck');
            $table->boolean('is_played')->default(false);
            $table->timestamp('played_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_cards');
    }

};
