<?php
// database/migrations/2025_09_29_000000_create_listas_rapidas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listas_rapidas', function (Blueprint $table) {
            $table->id();
            $table->string('grupo', 100)->index();
            $table->string('clave', 100);
            $table->string('valor', 255);
            $table->boolean('estado')->default(true)->index();
            $table->string('descripcion', 500)->nullable();
            $table->timestamps();

            $table->unique(['grupo', 'clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listas_rapidas');
    }
};
