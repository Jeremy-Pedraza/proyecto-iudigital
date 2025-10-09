<?php

namespace App\Http\Requests\Reglas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReglaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Si usas policies, puedes cambiar esto:
        // return $this->user()->can('update', $this->route('regla'));
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $regla = $this->route('regla');

        $rules = [
            'valor' => ['required'],
            'activa' => ['sometimes', 'boolean'],
        ];

        // Validaciones específicas según el tipo de regla
        if ($regla && $regla->tipo === 'numero') {
            $rules['valor'][] = 'numeric';

            if ($regla->valor_minimo !== null) {
                $rules['valor'][] = "min:{$regla->valor_minimo}";
            }

            if ($regla->valor_maximo !== null) {
                $rules['valor'][] = "max:{$regla->valor_maximo}";
            }
        }

        if ($regla && $regla->tipo === 'booleano') {
            $rules['valor'][] = Rule::in(['true', 'false', '1', '0', 'on', 'off']);
        }

        if ($regla && $regla->tipo === 'tiempo') {
            $rules['valor'][] = 'integer';

            if ($regla->valor_minimo !== null) {
                $rules['valor'][] = "min:{$regla->valor_minimo}";
            }

            if ($regla->valor_maximo !== null) {
                $rules['valor'][] = "max:{$regla->valor_maximo}";
            }
        }

        if ($regla && $regla->tipo === 'texto') {
            $rules['valor'][] = 'string';
            $rules['valor'][] = 'max:255';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'valor.required' => 'El valor de la regla es obligatorio.',
            'valor.numeric' => 'El valor debe ser un número.',
            'valor.integer' => 'El valor debe ser un número entero.',
            'valor.min' => 'El valor debe ser mayor o igual a :min.',
            'valor.max' => 'El valor debe ser menor o igual a :max.',
            'valor.in' => 'El valor debe ser verdadero o falso.',
            'activa.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'valor' => 'valor de la regla',
            'activa' => 'estado',
        ];
    }
}
