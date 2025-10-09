<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('tipo_periodo', ['diario', 'semanal', 'mensual'])->default('semanal');

            // Comercial asignado
            $table->foreignId('comercial_id')->constrained('users')->onDelete('cascade');

            // Restricciones
            $table->integer('max_paradas_dia')->default(20);
            $table->time('hora_inicio_jornada')->default('08:00:00');
            $table->time('hora_fin_jornada')->default('18:00:00');
            $table->integer('duracion_pausa_minutos')->default(60);

            // Filtros aplicados (JSON)
            $table->json('filtros_clientes')->nullable();

            // Opciones del algoritmo
            $table->enum('prioridad_criterio', ['distancia', 'tiempo', 'prioridad_cliente', 'balanceado'])->default('balanceado');
            $table->boolean('respetar_ventanas_horarias')->default(true);
            $table->boolean('balancear_carga')->default(true);

            // Métricas calculadas
            $table->decimal('distancia_total_km', 10, 2)->nullable();
            $table->integer('tiempo_total_minutos')->nullable();
            $table->integer('total_paradas')->default(0);
            $table->integer('dias_planificados')->default(0);

            // Estado
            $table->enum('estado', ['borrador', 'calculada', 'publicada', 'en_ejecucion', 'completada', 'cancelada'])->default('borrador');

            // Metadata
            $table->text('notas')->nullable();
            $table->timestamp('fecha_publicacion')->nullable();
            $table->foreignId('publicado_por')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['comercial_id', 'fecha_inicio', 'estado']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};
