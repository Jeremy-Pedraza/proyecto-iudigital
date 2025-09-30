<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();

            // Quién hizo la acción (opcional si hay procesos automáticos)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Qué recurso afectó (polimórfico)
            $table->morphs('auditable'); // auditable_id, auditable_type

            // Metadatos
            $table->string('module', 100);     // p.ej. 'usuarios', 'productos', 'roles'
            $table->string('action', 50);      // p.ej. 'create', 'update', 'delete', 'login'
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            // Datos
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            // Índices útiles para filtros
            $table->index(['module', 'action']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
