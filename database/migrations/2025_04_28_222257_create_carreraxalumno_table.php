<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarreraxalumnoTable extends Migration
{
    public function up(): void
    {
        Schema::create('carreraxalumno', function (Blueprint $table) {
            $table->string('dni_alumno', 10);
            $table->unsignedBigInteger('id_carrera');
            $table->string('matricula', 20)->unique();
            $table->unsignedBigInteger('id_condicion');

            $table->primary(['dni_alumno', 'id_carrera']);

            $table->foreign('dni_alumno')->references('dni')->on('alumnos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_carrera')->references('id_carrera')->on('carrera')->onUpdate('cascade');
            $table->foreign('id_condicion')->references('id_condicion')->on('condiciones')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carreraxalumno');
    }
}
