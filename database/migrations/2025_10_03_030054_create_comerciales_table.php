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
        Schema::create('comerciales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email', 150)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('codigo_empleado', 50)->nullable()->unique();
            $table->enum('departamento', [
                'ventas',
                'marketing',
                'atencion_cliente',
                'desarrollo_negocio'
            ])->nullable();
            $table->text('direccion')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->decimal('salario_base', 10, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices para mejorar el rendimiento
            $table->index('email');
            $table->index('codigo_empleado');
            $table->index('departamento');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comerciales');
    }
};
