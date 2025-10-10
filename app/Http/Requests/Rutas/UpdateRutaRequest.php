<?php

namespace App\Http\Requests\Rutas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRutaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Aquí puedes agregar lógica de autorización si es necesario
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'string', 'max:255'],
            'fecha' => ['sometimes', 'date', 'after_or_equal:today'],
            'estado' => ['sometimes', Rule::in(['borrador', 'planificada', 'publicada', 'en_curso', 'completada', 'cancelada'])],
            'hora_inicio' => ['nullable', 'date_format:H:i'],
            'hora_fin' => ['nullable', 'date_format:H:i', 'after:hora_inicio'],
            'notas' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la ruta es obligatorio.',
            'fecha.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}
