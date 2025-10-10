<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parada extends Model
{
    use SoftDeletes;

    protected $table = 'paradas';

    protected $fillable = [
        'ruta_id',
        'cliente_id',
        'fecha_planificada',
        'hora_estimada_llegada',
        'hora_estimada_salida',
        'duracion_estimada_minutos',
        'orden_secuencia',
        'distancia_desde_anterior_km',
        'tiempo_desde_anterior_minutos',
        'check_in',
        'check_out',
        'notas',
        'lat_check_in',
        'lng_check_in',
        'estado',
        'motivo_no_atencion',
        'notas_visita',
        'pedido_id',
        'cobro_id',
    ];

    protected $casts = [
        'fecha_planificada' => 'date',
        'duracion_estimada_minutos' => 'integer',
        'orden_secuencia' => 'integer',
        'distancia_desde_anterior_km' => 'decimal:2',
        'tiempo_desde_anterior_minutos' => 'integer',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'lat_check_in' => 'decimal:8',
        'lng_check_in' => 'decimal:8',
    ];

    // Relaciones
    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    // public function pedido(): BelongsTo
    // {
    //     return $this->belongsTo(Pedido::class);
    // }

    // public function cobro(): BelongsTo
    // {
    //     return $this->belongsTo(Cobro::class);
    // }

    // Scopes
    public function scopeFecha($query, ?string $fecha)
    {
        return $fecha ? $query->whereDate('fecha_planificada', $fecha) : $query;
    }

    public function scopeRuta($query, $rutaId)
    {
        return $rutaId ? $query->where('ruta_id', $rutaId) : $query;
    }

    public function scopeEstado($query, ?string $estado)
    {
        return $estado ? $query->where('estado', $estado) : $query;
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('fecha_planificada')->orderBy('orden_secuencia');
    }

    // Accessors
    public function getEsCompletadaAttribute(): bool
    {
        return $this->estado === 'completada';
    }

    public function getEsPendienteAttribute(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function getTieneCheckInAttribute(): bool
    {
        return !is_null($this->check_in);
    }

    public function getDuracionRealMinutosAttribute(): ?int
    {
        if (!$this->check_in || !$this->check_out) {
            return null;
        }
        return $this->check_out->diffInMinutes($this->check_in);
    }

    public function getHoraEstimadaLlegadaFormateadaAttribute(): ?string
    {
        return $this->hora_estimada_llegada
            ? \Carbon\Carbon::parse($this->hora_estimada_llegada)->format('H:i')
            : null;
    }

    // Métodos de negocio
    public function realizarCheckIn(float $lat, float $lng): bool
    {
        if ($this->tiene_check_in) {
            return false;
        }

        $this->check_in = now();
        $this->lat_check_in = $lat;
        $this->lng_check_in = $lng;
        $this->estado = 'en_ruta';

        return $this->save();
    }

    public function realizarCheckOut(?string $notas = null): bool
    {
        if (!$this->tiene_check_in || $this->check_out) {
            return false;
        }

        $this->check_out = now();
        $this->estado = 'completada';
        if ($notas) {
            $this->notas_visita = $notas;
        }

        return $this->save();
    }

    public function marcarNoAtendida(string $motivo, ?string $notas = null): bool
    {
        $this->estado = 'no_atendida';
        $this->motivo_no_atencion = $motivo;
        if ($notas) {
            $this->notas_visita = $notas;
        }

        return $this->save();
    }

    public function validarDistanciaCheckIn(float $latCliente, float $lngCliente, int $radioMetros = 100): bool
    {
        if (!$this->lat_check_in || !$this->lng_check_in) {
            return false;
        }

        // Fórmula de Haversine para calcular distancia
        $radioTierra = 6371000; // metros

        $dLat = deg2rad($latCliente - $this->lat_check_in);
        $dLng = deg2rad($lngCliente - $this->lng_check_in);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($this->lat_check_in)) * cos(deg2rad($latCliente)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distancia = $radioTierra * $c;

        return $distancia <= $radioMetros;
    }
}
