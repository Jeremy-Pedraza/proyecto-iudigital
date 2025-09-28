<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();       // Admin, Supervisor, Cobranzas...
            $table->string('slug')->unique();       // admin, supervisor, cobranzas
            $table->string('description')->nullable();
            // $table->boolean('is_active')->default(true); // <- opcional
            $table->timestamps();
            // $table->softDeletes(); // <- si quieres borrado lógico
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
