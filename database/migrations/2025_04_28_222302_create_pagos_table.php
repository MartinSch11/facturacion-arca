<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->unsignedBigInteger('id_factura');
            $table->timestamps();

            $table->foreign('id_factura')->references('nro_factura')->on('facturas')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
}
