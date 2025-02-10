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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade');
            $table->string('name'); // Nombre de la sala
            $table->dateTime('start_time'); // Fecha y hora de inicio
            $table->decimal('total_prizes', 10, 2); // Premios totales a repartir
            $table->decimal('card_price', 10, 2); // Precio por cartón
            $table->integer('total_cards'); // Cantidad total de cartones
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
