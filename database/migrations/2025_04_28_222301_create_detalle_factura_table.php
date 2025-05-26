<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleFacturaTable extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_factura', function (Blueprint $table) {
            $table->id('id_detalle');
            $table->unsignedBigInteger('nro_boleta');
            $table->unsignedBigInteger('nro_factura');

            $table->foreign('nro_boleta')->references('nro_boleta')->on('boleta')->onUpdate('cascade');
            $table->foreign('nro_factura')->references('nro_factura')->on('facturas')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_factura');
    }
}
