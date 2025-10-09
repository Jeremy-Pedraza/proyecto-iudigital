<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regla extends Model
{
    protected $table = 'reglas';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo',
        'valor',
        'valor_minimo',
        'valor_maximo',
        'unidad',
        'categoria',
        'activa',
        'editable',
        'orden'
    ];

    protected $casts = [
        'valor_minimo' => 'decimal:2',
        'valor_maximo' => 'decimal:2',
        'activa' => 'boolean',
        'editable' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Obtiene el valor parseado según el tipo de regla
     */
    public function getValorParseadoAttribute()
    {
        return match ($this->tipo) {
            'numero' => (float) $this->valor,
            'booleano' => filter_var($this->valor, FILTER_VALIDATE_BOOLEAN),
            'tiempo' => (int) $this->valor,
            'json' => json_decode($this->valor, true),
            default => $this->valor,
        };
    }

    /**
     * Valida si un valor está dentro del rango permitido
     */
    public function validarValor($valor): bool
    {
        if ($this->tipo === 'numero' || $this->tipo === 'tiempo') {
            $numerico = (float) $valor;

            if ($this->valor_minimo !== null && $numerico < $this->valor_minimo) {
                return false;
            }

            if ($this->valor_maximo !== null && $numerico > $this->valor_maximo) {
                return false;
            }
        }

        return true;
    }

    // Scopes de filtrado
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeEditables($query)
    {
        return $query->where('editable', true);
    }

    public function scopeCategoria($query, ?string $categoria)
    {
        return $categoria ? $query->where('categoria', $categoria) : $query;
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
}
