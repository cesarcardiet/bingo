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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre completo del agente
            $table->string('id_number')->unique(); // Número de Cédula de Identidad (C.I.)
            $table->string('password'); // Contraseña
            $table->string('phone')->unique(); // Número de teléfono asociado a Pago Móvil
            $table->enum('bank_name', [ // Lista de bancos en Venezuela con su código
                'Banco de Venezuela (0102)',
                'Banesco (0134)',
                'Banco Mercantil (0105)',
                'Banca Amiga (0172)',
                'BBVA Provincial (0108)',
                'Banco del Tesoro (0163)',
                'Banco Nacional de Crédito (BNC) (0191)',
                'Banco Venezolano de Crédito (BVC) (0104)',
                'Banco Fondo Común (0151)',
                'Banco de la Fuerza Armada Nacional Bolivariana (BANFANB) (0177)',
            ]); 
            $table->string('email')->unique(); // Correo electrónico
            $table->string('referral_id')->unique(); // ID de referido único (Generado Automáticamente)
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
