<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleboletaTable extends Migration
{
    public function up(): void
    {
        Schema::create('detalleboleta', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('nro_boleta');
            $table->unsignedBigInteger('concepto');
            $table->decimal('precio', 10, 2);

            $table->foreign('nro_boleta')->references('nro_boleta')->on('boleta')->onUpdate('cascade');
            $table->foreign('concepto')->references('id_concepto')->on('concepto')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalleboleta');
    }
}
