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
        Schema::table('raffles', function (Blueprint $table) {
            $table->dropForeign(['agent_id']); // Eliminar la relación con agentes
            $table->dropColumn('agent_id'); // Eliminar la columna
            $table->foreignId('room_id')->after('id')->constrained('rooms')->onDelete('cascade'); // Nueva relación con salas
            $table->integer('order')->after('prize')->default(1); // Agregar orden del sorteo en la sala
            $table->enum('game_type', ['Cartón lleno', 'Línea', 'Cruz', 'Cuatro esquinas', 'Esquinas dobles', 'X', 'Patrón personalizado'])->change(); // Actualizar tipos de juego
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn(['room_id', 'order']);
            $table->foreignId('agent_id')->constrained('agents')->onDelete('cascade'); // Restaurar relación con agentes
        });
    }
};
