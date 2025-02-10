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
        Schema::create('bingo_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('player_id')->nullable(); // Se asigna cuando se compra
            $table->string('card_number')->unique(); // Número único del cartón
            $table->json('card_data'); // Almacena la estructura del cartón en JSON
            $table->enum('status', ['Disponible', 'Comprado'])->default('Disponible');
            $table->timestamps();
    
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('player_id')->references('id')->on('players')->onDelete('set null');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bingo_cards');
    }
};
