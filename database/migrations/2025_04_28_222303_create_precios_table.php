<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreciosTable extends Migration
{
    public function up(): void
    {
        Schema::create('precios', function (Blueprint $table) {
            $table->id('id_precio');
            $table->unsignedBigInteger('id_carrera');
            $table->decimal('precio', 10, 2);

            $table->foreign('id_carrera')->references('id_carrera')->on('carrera')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('precios');
    }
}
