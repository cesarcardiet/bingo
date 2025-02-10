<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('raffle_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained('raffles')->onDelete('cascade'); // Relación con el sorteo
            $table->integer('number'); // Número generado (1-75)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('raffle_numbers');
    }
};
