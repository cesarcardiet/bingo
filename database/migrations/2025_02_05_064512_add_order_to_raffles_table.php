<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->integer('order')->after('prize')->default(1); // Añadir la columna con un valor por defecto
        });
    }

    public function down()
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
