<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUltimosNumerosTable extends Migration
{
    public function up(): void
    {
        Schema::create('ultimos_numeros', function (Blueprint $table) {
            $table->id();
            $table->string('punto_venta', 10);
            $table->unsignedBigInteger('ultimo_numero');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ultimos_numeros');
    }
}
