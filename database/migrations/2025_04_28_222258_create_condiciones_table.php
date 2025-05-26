<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCondicionesTable extends Migration
{
    public function up(): void
    {
        Schema::create('condiciones', function (Blueprint $table) {
            $table->id('id_condicion');
            $table->string('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condiciones');
    }
}
