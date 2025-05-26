<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConceptoTable extends Migration
{
    public function up(): void
    {
        Schema::create('concepto', function (Blueprint $table) {
            $table->id('id_concepto');
            $table->string('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concepto');
    }
}
