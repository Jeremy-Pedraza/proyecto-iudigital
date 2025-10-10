<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_id')->constrained('rutas')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->integer('orden')->default(0);
            $table->enum('estado', ['pendiente', 'en_curso', 'completada', 'no_atendida'])
                  ->default('pendiente');

            // Tiempos planificados
            $table->timestamp('hora_estimada_llegada')->nullable();

            // Tiempos reales (check-in/check-out)
            $table->timestamp('hora_real_llegada')->nullable();
            $table->timestamp('hora_salida')->nullable();

            // Métricas de distancia y tiempo
            $table->decimal('distancia_desde_anterior_km', 8, 2)->nullable();
            $table->integer('tiempo_desde_anterior_min')->nullable();
            $table->integer('duracion_real_min')->nullable();

            // Información adicional
            $table->text('notas')->nullable();
            $table->string('motivo_no_atencion')->nullable();

            // Geolocalización del check-in
            $table->decimal('lat_checkin', 10, 7)->nullable();
            $table->decimal('lng_checkin', 10, 7)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['ruta_id', 'orden']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paradas');
    }
};
