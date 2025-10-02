<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'razon_social',
        'nombre_fantasia',
        'documento',
        'email',
        'telefono',
        'direccion',
        'ciudad',
        'lat',
        'lng',
        'frecuencia_visita',
        'ventana_horaria',
        'prioridad',
        'estado',
        'comercial_id',
        'notas'
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'prioridad' => 'integer',
    ];

    // Relaciones
    public function comercial()
    {
        return $this->belongsTo(User::class, 'comercial_id');
    }
    // public function pedidos()
    // {
    //     return $this->hasMany(\App\Domain\Ventas\Entities\Pedido::class);
    // }
    // public function cobros()
    // {
    //     return $this->hasMany(\App\Domain\Cobranzas\Entities\Cobro::class);
    // }

    // Scopes de filtro/orden
    public function scopeSearch($q, ?string $s)
    {
        if (!$s) return $q;
        return $q->where(function ($qq) use ($s) {
            $qq->where('razon_social', 'like', "%$s%")
                ->orWhere('nombre_fantasia', 'like', "%$s%")
                ->orWhere('documento', 'like', "%$s%");
        });
    }
    public function scopeCiudad($q, ?string $c)
    {
        return $c ? $q->where('ciudad', $c) : $q;
    }
    public function scopeEstado($q, ?string $e)
    {
        return $e ? $q->where('estado', $e) : $q;
    }
    public function scopeComercial($q, $id)
    {
        return $id ? $q->where('comercial_id', $id) : $q;
    }
    public function scopeFrecuencia($q, ?string $f)
    {
        return $f ? $q->where('frecuencia_visita', $f) : $q;
    }
    public function scopeSort($q, ?string $by, ?string $dir)
    {
        $by = in_array($by, ['razon_social', 'ciudad', 'prioridad', 'created_at']) ? $by : 'created_at';
        $dir = $dir === 'asc' ? 'asc' : 'desc';
        return $q->orderBy($by, $dir);
    }
}
