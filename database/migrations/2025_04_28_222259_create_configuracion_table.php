<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfiguracionTable extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id('id_config');
            $table->string('cuit', 20);
            $table->string('punto_venta', 10);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
}
