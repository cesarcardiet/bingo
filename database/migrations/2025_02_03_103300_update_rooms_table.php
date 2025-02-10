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
        Schema::table('rooms', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name'); // Agregar descripción
            $table->integer('max_players')->nullable()->after('total_cards'); // Agregar límite de jugadores
            $table->enum('status', ['active', 'inactive'])->default('active')->after('max_players'); // Estado de la sala
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['description', 'max_players', 'status']);
        });
    }
};
