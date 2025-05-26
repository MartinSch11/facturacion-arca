<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnosTable extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->string('dni', 10)->primary();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cuit', 20)->nullable();
            $table->string('direccion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
}
