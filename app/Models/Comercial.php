<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comercial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'comerciales';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'codigo_empleado',
        'departamento',
        'direccion',
        'fecha_ingreso',
        'salario_base',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_ingreso' => 'date',
        'salario_base' => 'decimal:2',
    ];

    protected $dates = [
        'fecha_ingreso',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * Scope para comerciales activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por departamento
     */
    public function scopeDepartamento($query, $departamento)
    {
        return $query->where('departamento', $departamento);
    }
}
