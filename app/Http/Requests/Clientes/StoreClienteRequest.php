<?php

// app/Http/Requests/Clientes/StoreClienteRequest.php
namespace App\Http\Requests\Clientes;

use App\Models\Cliente;
use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Cliente::class);
    }
    public function rules(): array
    {
        return [
            'razon_social' => 'required|string|max:150',
            'nombre_fantasia' => 'nullable|string|max:150',
            'documento' => 'required|string|max:30|unique:clientes,documento',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:190',
            'ciudad' => 'nullable|string|max:100',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'frecuencia_visita' => 'required|in:diaria,semanal,quincenal,mensual',
            'ventana_horaria' => 'nullable|regex:/^\d{2}:\d{2}-\d{2}:\d{2}$/',
            'prioridad' => 'required|integer|min:1|max:5',
            'estado' => 'required|in:activo,inactivo',
            'comercial_id' => 'nullable|exists:users,id',
            'notas' => 'nullable|string',
        ];
    }
}
