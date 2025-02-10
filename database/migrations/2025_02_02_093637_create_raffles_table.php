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
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade'); // Relación con el agente
            $table->string('name'); // Nombre del sorteo
            $table->dateTime('start_time')->nullable();
            $table->enum('game_type', ['Cartón lleno', 'Cruz', 'Cuatro esquinas']); // Tipo de juego
            $table->integer('total_cards'); // Cantidad de cartones disponibles
            $table->decimal('prize', 10, 2); // Premio en Bs
            $table->enum('status', ['Pendiente', 'En curso', 'Finalizado'])->default('Pendiente'); // Estado del sorteo
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raffles');
    }
};
