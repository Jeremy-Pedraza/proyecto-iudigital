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

            // Planificación
            $table->date('fecha_planificada');
            $table->time('hora_estimada_llegada')->nullable();
            $table->time('hora_estimada_salida')->nullable();
            $table->integer('duracion_estimada_minutos')->default(30);
            $table->integer('orden_secuencia')->default(0);

            // Métricas de desplazamiento
            $table->decimal('distancia_desde_anterior_km', 10, 2)->nullable();
            $table->integer('tiempo_desde_anterior_minutos')->nullable();

            // Ejecución (check-in/out)
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->decimal('lat_check_in', 10, 7)->nullable();
            $table->decimal('lng_check_in', 10, 7)->nullable();

            // Estado y resultados
            $table->enum('estado', ['pendiente', 'en_ruta', 'completada', 'no_atendida'])
                  ->default('pendiente');
            $table->string('motivo_no_atencion')->nullable();
            $table->text('notas')->nullable();
            $table->text('notas_visita')->nullable();

            // Relaciones con pedidos/cobros (opcional)
            //$table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            //$table->foreignId('cobro_id')->nullable()->constrained('cobros')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['ruta_id', 'fecha_planificada', 'orden_secuencia']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paradas');
    }
};
