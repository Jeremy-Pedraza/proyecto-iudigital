<?php

namespace App\Http\Requests\Rutas;

use Illuminate\Foundation\Http\FormRequest;

class ReordenarParadasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'orden' => ['required', 'array', 'min:1'],
            'orden.*' => ['required', 'integer', 'exists:paradas,id']
        ];
    }

    public function messages(): array
    {
        return [
            'orden.required' => 'Debe proporcionar un orden de paradas.',
            'orden.array' => 'El orden debe ser un arreglo.',
            'orden.*.exists' => 'Una o más paradas no existen.',
        ];
    }
}
