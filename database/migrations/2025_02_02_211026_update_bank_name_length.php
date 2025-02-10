<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->string('bank_name', 100)->change(); // Aumenta el tamaño a 100 caracteres
        });
    }

    public function down()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->string('bank_name', 50)->change(); // Restaura el tamaño original (ajústalo si era diferente)
        });
    }
};
