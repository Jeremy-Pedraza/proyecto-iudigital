<?php

namespace App\Http\Requests\Comerciales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComercialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $comercialId = $this->route('comerciale'); // nombre del parámetro en la ruta

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:comerciales,email,' . $comercialId,
            'telefono' => 'nullable|string|max:50',
            'documento' => 'required|string|unique:comerciales,documento,' . $comercialId,
            'zona_id' => 'nullable|exists:zonas,id',
            'capacidad_paradas_dia' => 'required|integer|min:1|max:50',
            'estado' => 'required|in:activo,inactivo',
            'user_id' => 'nullable|exists:users,id',
            'notas' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'email.unique' => 'Este email ya está registrado.',
            'documento.required' => 'El documento es obligatorio.',
            'documento.unique' => 'Este documento ya está registrado.',
            'capacidad_paradas_dia.required' => 'La capacidad de paradas es obligatoria.',
            'capacidad_paradas_dia.min' => 'La capacidad debe ser al menos 1.',
            'estado.required' => 'El estado es obligatorio.'
        ];
    }
}
