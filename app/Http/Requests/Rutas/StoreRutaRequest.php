<?php

namespace App\Http\Requests\Rutas;

use Illuminate\Foundation\Http\FormRequest;

class StoreRutaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('crear_rutas'); // Ajustar según tus políticas
    }

    public function rules(): array
    {
        return [
            // Datos básicos
            'nombre' => 'nullable|string|max:200',
            'comercial_id' => 'required|exists:users,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'tipo_periodo' => 'required|in:diario,semanal,mensual',

            // Restricciones de jornada
            'max_paradas_dia' => 'required|integer|min:1|max:50',
            'hora_inicio_jornada' => 'required|date_format:H:i',
            'hora_fin_jornada' => 'required|date_format:H:i|after:hora_inicio_jornada',
            'duracion_pausa_minutos' => 'required|integer|min:0|max:180',

            // Filtros de clientes
            'filtros_clientes.ciudad' => 'nullable|string|max:100',
            'filtros_clientes.frecuencia' => 'nullable|in:diaria,semanal,quincenal,mensual',
            'filtros_clientes.prioridad_min' => 'nullable|integer|min:1|max:5',

            // Opciones del algoritmo
            'prioridad_criterio' => 'required|in:distancia,tiempo,prioridad_cliente,balanceado',
            'respetar_ventanas_horarias' => 'boolean',
            'balancear_carga' => 'boolean',

            // Notas
            'notas' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'comercial_id.required' => 'Debe seleccionar un comercial',
            'comercial_id.exists' => 'El comercial seleccionado no existe',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy',
            'fecha_fin.required' => 'La fecha de fin es obligatoria',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'max_paradas_dia.required' => 'Debe especificar el máximo de paradas por día',
            'max_paradas_dia.min' => 'Debe haber al menos 1 parada por día',
            'max_paradas_dia.max' => 'No se pueden planificar más de 50 paradas por día',
            'hora_fin_jornada.after' => 'La hora de fin debe ser posterior a la hora de inicio',
        ];
    }

    public function attributes(): array
    {
        return [
            'comercial_id' => 'comercial',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'max_paradas_dia' => 'máximo de paradas por día',
            'hora_inicio_jornada' => 'hora de inicio de jornada',
            'hora_fin_jornada' => 'hora de fin de jornada',
            'duracion_pausa_minutos' => 'duración de pausa',
            'prioridad_criterio' => 'criterio de priorización',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalizar formato de horas si vienen sin segundos
        if ($this->has('hora_inicio_jornada') && strlen($this->hora_inicio_jornada) === 5) {
            $this->merge(['hora_inicio_jornada' => $this->hora_inicio_jornada . ':00']);
        }

        if ($this->has('hora_fin_jornada') && strlen($this->hora_fin_jornada) === 5) {
            $this->merge(['hora_fin_jornada' => $this->hora_fin_jornada . ':00']);
        }

        // Convertir checkboxes a booleanos
        $this->merge([
            'respetar_ventanas_horarias' => $this->boolean('respetar_ventanas_horarias', true),
            'balancear_carga' => $this->boolean('balancear_carga', true),
        ]);

        // Construir array de filtros
        $filtros = [];
        if ($this->has('ciudad')) {
            $filtros['ciudad'] = $this->ciudad;
        }
        if ($this->has('frecuencia_filtro')) {
            $filtros['frecuencia'] = $this->frecuencia_filtro;
        }
        if ($this->has('prioridad_min')) {
            $filtros['prioridad_min'] = $this->prioridad_min;
        }

        if (!empty($filtros)) {
            $this->merge(['filtros_clientes' => $filtros]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que la fecha de inicio sea un día laborable
            $fechaInicio = \Carbon\Carbon::parse($this->fecha_inicio);
            if ($fechaInicio->isWeekend()) {
                $validator->errors()->add(
                    'fecha_inicio',
                    'La fecha de inicio debe ser un día laborable'
                );
            }

            // Validar duración de jornada mínima
            if ($this->hora_inicio_jornada && $this->hora_fin_jornada) {
                $inicio = \Carbon\Carbon::parse($this->hora_inicio_jornada);
                $fin = \Carbon\Carbon::parse($this->hora_fin_jornada);
                $duracion = $fin->diffInHours($inicio);

                if ($duracion < 4) {
                    $validator->errors()->add(
                        'hora_fin_jornada',
                        'La jornada debe ser de al menos 4 horas'
                    );
                }
            }
        });
    }
}
