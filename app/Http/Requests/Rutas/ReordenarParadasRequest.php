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
            'orden.min' => 'Debe haber al menos una parada.',
            'orden.*.required' => 'Cada elemento del orden es obligatorio.',
            'orden.*.integer' => 'Los IDs deben ser números enteros.',
            'orden.*.exists' => 'Una o más paradas no existen.',
        ];
    }

    /**
     * Validación adicional para asegurar que todas las paradas pertenecen a la ruta
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $ruta = $this->route('ruta');
            $ordenIds = $this->input('orden', []);

            if (!$ruta) {
                return;
            }

            // Verificar que todas las paradas pertenezcan a esta ruta
            $paradasRuta = $ruta->paradas()->pluck('id')->toArray();
            $idsInvalidos = array_diff($ordenIds, $paradasRuta);

            if (!empty($idsInvalidos)) {
                $validator->errors()->add(
                    'orden',
                    'Algunas paradas no pertenecen a esta ruta.'
                );
            }
        });
    }
}
