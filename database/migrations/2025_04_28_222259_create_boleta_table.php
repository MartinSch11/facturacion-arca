<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoletaTable extends Migration
{
    public function up(): void
    {
        Schema::create('boleta', function (Blueprint $table) {
            $table->id('nro_boleta');
            $table->string('matricula', 20);
            $table->string('estado', 50);
            $table->date('fecha_pago')->nullable();
            $table->decimal('importe_pagado', 10, 2)->nullable();
            $table->unsignedBigInteger('id_emision');
            $table->unsignedBigInteger('id_concepto');
            $table->string('nombre_detallado')->nullable();

            $table->foreign('matricula')->references('matricula')->on('carreraxalumno')->onUpdate('cascade');
            $table->foreign('id_emision')->references('id_emision')->on('emisionboleta')->onUpdate('cascade');
            $table->foreign('id_concepto')->references('id_concepto')->on('concepto')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boleta');
    }
}
