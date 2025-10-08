<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comercial extends Model
{
    use SoftDeletes;

    protected $table = 'comerciales';

    protected $fillable = [
        'name',
        'email',
        'telefono',
        'documento',
        'zona_id',
        'capacidad_paradas_dia',
        'estado',
        'user_id',
        'notas'
    ];

    protected $casts = [
        'capacidad_paradas_dia' => 'integer',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'comercial_id');
    }

    // Scopes de filtro/orden
    public function scopeSearch($q, ?string $s)
    {
        if (!$s) return $q;
        return $q->where(function ($qq) use ($s) {
            $qq->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('documento', 'like', "%$s%");
        });
    }

    public function scopeEstado($q, ?string $e)
    {
        return $e ? $q->where('estado', $e) : $q;
    }

    public function scopeZona($q, $id)
    {
        return $id ? $q->where('zona_id', $id) : $q;
    }

    public function scopeSort($q, ?string $by, ?string $dir)
    {
        $by = in_array($by, ['name', 'email', 'capacidad_paradas_dia', 'created_at']) ? $by : 'created_at';
        $dir = $dir === 'asc' ? 'asc' : 'desc';
        return $q->orderBy($by, $dir);
    }
}
