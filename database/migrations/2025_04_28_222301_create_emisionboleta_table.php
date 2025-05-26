<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmisionboletaTable extends Migration
{
    public function up(): void
    {
        Schema::create('emisionboleta', function (Blueprint $table) {
            $table->id('id_emision');
            $table->date('fecha_primer_vencimiento');
            $table->date('fecha_segundo_vencimiento')->nullable();
            $table->date('fecha_tercer_vencimiento')->nullable();
            $table->unsignedBigInteger('boleta_inicial');
            $table->unsignedBigInteger('boleta_final');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emisionboleta');
    }
}
