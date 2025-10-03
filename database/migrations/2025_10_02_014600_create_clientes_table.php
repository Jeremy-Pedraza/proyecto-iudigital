<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social');
            $table->string('nombre_fantasia')->nullable();
            $table->string('documento', 30)->unique(); // NIT/CC/RUC
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->enum('frecuencia_visita', ['diaria', 'semanal', 'quincenal', 'mensual'])->default('semanal');
            $table->string('ventana_horaria')->nullable(); // "09:00-12:00"
            $table->unsignedTinyInteger('prioridad')->default(3); // 1 alta, 5 baja
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->foreignId('comercial_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notas')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['ciudad', 'estado', 'comercial_id']);
            // Índice compuesto para lat y lng (en lugar de SPATIAL)
            $table->index(['lat', 'lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
