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
        Schema::create('reglas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 100)->unique()->comment('Identificador único de la regla');
            $table->string('nombre', 200)->comment('Nombre descriptivo de la regla');
            $table->text('descripcion')->nullable()->comment('Descripción detallada');
            $table->enum('tipo', ['numero', 'booleano', 'texto', 'tiempo', 'json'])->comment('Tipo de dato del valor');
            $table->text('valor')->comment('Valor actual de la regla');
            $table->decimal('valor_minimo', 10, 2)->nullable()->comment('Valor mínimo permitido');
            $table->decimal('valor_maximo', 10, 2)->nullable()->comment('Valor máximo permitido');
            $table->string('unidad', 50)->nullable()->comment('Unidad de medida (ej: minutos, km, paradas)');
            $table->enum('categoria', [
                'capacidad',
                'tiempo',
                'distancia',
                'optimizacion',
                'restricciones',
                'penalizaciones',
                'general'
            ])->default('general')->comment('Categoría de la regla');
            $table->boolean('activa')->default(true)->comment('Indica si la regla está activa');
            $table->boolean('editable')->default(true)->comment('Indica si puede ser modificada por usuarios');
            $table->integer('orden')->default(0)->comment('Orden de visualización');
            $table->timestamps();

            $table->index('categoria');
            $table->index('activa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reglas');
    }
};
