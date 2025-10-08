<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zona extends Model
{
    use SoftDeletes;

    protected $table = 'zonas';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'ciudades',
        'centro_lat',
        'centro_lng',
        'radio_km',
        'poligono',
        'color',
        'estado',
        'prioridad',
        'notas'
    ];

    protected $casts = [
        'ciudades' => 'array',
        'poligono' => 'array',
        'centro_lat' => 'decimal:7',
        'centro_lng' => 'decimal:7',
        'prioridad' => 'integer',
        'radio_km' => 'integer',
    ];

    public function comerciales()
    {
        return $this->hasMany(Comercial::class, 'zona_id');
    }


    // Scopes de filtro/orden
    public function scopeSearch($q, ?string $s)
    {
        if (!$s) return $q;
        return $q->where(function ($qq) use ($s) {
            $qq->where('nombre', 'like', "%$s%")
                ->orWhere('codigo', 'like', "%$s%")
                ->orWhere('descripcion', 'like', "%$s%");
        });
    }

    public function scopeEstado($q, ?string $e)
    {
        return $e ? $q->where('estado', $e) : $q;
    }

    public function scopeCiudad($q, ?string $c)
    {
        if (!$c) return $q;
        return $q->whereJsonContains('ciudades', $c);
    }

    public function scopeSort($q, ?string $by, ?string $dir)
    {
        $by = in_array($by, ['nombre', 'codigo', 'prioridad', 'created_at']) ? $by : 'nombre';
        $dir = $dir === 'desc' ? 'desc' : 'asc';
        return $q->orderBy($by, $dir);
    }

    // Accessor para verificar si está activa
    public function getIsActivaAttribute(): bool
    {
        return $this->estado === 'activa';
    }

    // Helper para verificar si un punto está dentro de la zona
    public function contienepunto(float $lat, float $lng): bool
    {
        if ($this->radio_km && $this->centro_lat && $this->centro_lng) {
            return $this->puntoEnRadio($lat, $lng);
        }

        if ($this->poligono && count($this->poligono) > 0) {
            return $this->puntoEnPoligono($lat, $lng);
        }

        return false;
    }

    private function puntoEnRadio(float $lat, float $lng): bool
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat - $this->centro_lat);
        $dLng = deg2rad($lng - $this->centro_lng);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($this->centro_lat)) * cos(deg2rad($lat)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance <= $this->radio_km;
    }

    private function puntoEnPoligono(float $lat, float $lng): bool
    {
        $vertices = $this->poligono;
        $intersections = 0;
        $count = count($vertices);

        for ($i = 0; $i < $count; $i++) {
            $v1 = $vertices[$i];
            $v2 = $vertices[($i + 1) % $count];

            if (
                $lng > min($v1['lng'], $v2['lng']) &&
                $lng <= max($v1['lng'], $v2['lng']) &&
                $lat <= max($v1['lat'], $v2['lat']) &&
                $v1['lng'] != $v2['lng']
            ) {

                $xinters = ($lng - $v1['lng']) * ($v2['lat'] - $v1['lat']) /
                    ($v2['lng'] - $v1['lng']) + $v1['lat'];

                if ($v1['lat'] == $v2['lat'] || $lat <= $xinters) {
                    $intersections++;
                }
            }
        }

        return ($intersections % 2) != 0;
    }
}
