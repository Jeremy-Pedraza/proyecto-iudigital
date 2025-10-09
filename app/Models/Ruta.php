<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruta extends Model
{
    use SoftDeletes;

    protected $table = 'rutas';

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'tipo_periodo',
        'comercial_id',
        'max_paradas_dia',
        'hora_inicio_jornada',
        'hora_fin_jornada',
        'duracion_pausa_minutos',
        'filtros_clientes',
        'prioridad_criterio',
        'respetar_ventanas_horarias',
        'balancear_carga',
        'distancia_total_km',
        'tiempo_total_minutos',
        'total_paradas',
        'dias_planificados',
        'estado',
        'notas',
        'fecha_publicacion',
        'publicado_por',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'max_paradas_dia' => 'integer',
        'duracion_pausa_minutos' => 'integer',
        'filtros_clientes' => 'array',
        'respetar_ventanas_horarias' => 'boolean',
        'balancear_carga' => 'boolean',
        'distancia_total_km' => 'decimal:2',
        'tiempo_total_minutos' => 'integer',
        'total_paradas' => 'integer',
        'dias_planificados' => 'integer',
        'fecha_publicacion' => 'datetime',
    ];

    // Relaciones
    public function comercial(): BelongsTo
    {
        return $this->belongsTo(User::class, 'comercial_id');
    }

    public function publicadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publicado_por');
    }

    public function paradas(): HasMany
    {
        return $this->hasMany(Parada::class)->orderBy('fecha_planificada')->orderBy('orden_secuencia');
    }

    // Scopes
    public function scopeEstado($query, ?string $estado)
    {
        return $estado ? $query->where('estado', $estado) : $query;
    }

    public function scopeComercial($query, $comercialId)
    {
        return $comercialId ? $query->where('comercial_id', $comercialId) : $query;
    }

    public function scopePeriodo($query, ?string $fechaInicio, ?string $fechaFin)
    {
        if ($fechaInicio) {
            $query->where('fecha_inicio', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $query->where('fecha_fin', '<=', $fechaFin);
        }
        return $query;
    }

    public function scopeRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Mutators
    public function getNombreCompletoAttribute(): string
    {
        return sprintf(
            '%s - %s (%s a %s)',
            $this->nombre,
            $this->comercial->name ?? 'Sin asignar',
            $this->fecha_inicio->format('d/m/Y'),
            $this->fecha_fin->format('d/m/Y')
        );
    }

    public function getEsBorradorAttribute(): bool
    {
        return $this->estado === 'borrador';
    }

    public function getEsPublicadaAttribute(): bool
    {
        return in_array($this->estado, ['publicada', 'en_ejecucion', 'completada']);
    }

    public function getDuracionJornadaMinutosAttribute(): int
    {
        $inicio = \Carbon\Carbon::parse($this->hora_inicio_jornada);
        $fin = \Carbon\Carbon::parse($this->hora_fin_jornada);
        return $fin->diffInMinutes($inicio);
    }

    // Métodos de negocio
    public function calcularMetricas(): void
    {
        $paradas = $this->paradas;

        $this->total_paradas = $paradas->count();
        $this->distancia_total_km = $paradas->sum('distancia_desde_anterior_km');
        $this->tiempo_total_minutos = $paradas->sum('tiempo_desde_anterior_minutos') +
            $paradas->sum('duracion_estimada_minutos');
        $this->dias_planificados = $paradas->pluck('fecha_planificada')->unique()->count();

        $this->save();
    }

    public function publicar(int $usuarioId): bool
    {
        if ($this->estado !== 'calculada') {
            return false;
        }

        $this->estado = 'publicada';
        $this->fecha_publicacion = now();
        $this->publicado_por = $usuarioId;

        return $this->save();
    }
}
