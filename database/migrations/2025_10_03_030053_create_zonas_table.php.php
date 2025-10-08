<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('codigo', 20)->unique()->nullable();
            $table->text('descripcion')->nullable();
            $table->json('ciudades')->nullable(); // Array de ciudades que pertenecen a esta zona
            $table->decimal('centro_lat', 10, 7)->nullable();
            $table->decimal('centro_lng', 10, 7)->nullable();
            $table->integer('radio_km')->nullable()->comment('Radio en kilómetros desde el centro');
            $table->json('poligono')->nullable()->comment('Coordenadas del polígono [{"lat":x,"lng":y},...]');
            $table->string('color', 7)->default('#3498db')->comment('Color en hex para visualización');
            $table->enum('estado', ['activa', 'inactiva'])->default('activa');
            $table->integer('prioridad')->default(1)->comment('1=Alta, 5=Baja');
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('estado');
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
