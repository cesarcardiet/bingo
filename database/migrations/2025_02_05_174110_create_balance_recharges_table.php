<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceRechargesTable extends Migration
{
    public function up()
    {
        Schema::create('balance_recharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->string('bank_name');
            $table->string('reference_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('receipt_image');
            $table->enum('status', ['Pendiente', 'Aprobada', 'Rechazada'])->default('Pendiente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('balance_recharges');
    }
}
