<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaRapida extends Model
{
    protected $table = 'listas_rapidas';

    protected $fillable = [
        'grupo',   // p.ej. "ESTADOS_PEDIDO"
        'clave',   // p.ej. "PENDIENTE"
        'valor',   // p.ej. "Pendiente"
        'estado',  // boolean: activo/inactivo
        'descripcion'
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
}
