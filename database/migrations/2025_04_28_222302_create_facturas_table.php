<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturasTable extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id('nro_factura');
            $table->string('matricula', 20);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->decimal('importe_pagado', 10, 2)->nullable();
            $table->date('fecha_pago')->nullable();
            $table->string('cae', 50)->nullable();
            $table->date('cae_vencimiento')->nullable();
            $table->string('forma_pago', 50);

            $table->foreign('matricula')->references('matricula')->on('carreraxalumno')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
}
