<?php

namespace App\Http\Requests\Zonas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// ═══════════════════════════════════════════════════════════════════════════════
// StoreZonaRequest
// ═══════════════════════════════════════════════════════════════════════════════
class StoreZonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajustar según policies
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:zonas,nombre'],
            'codigo' => ['nullable', 'string', 'max:20', 'unique:zonas,codigo'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'ciudades' => ['nullable', 'array'],
            'ciudades.*' => ['string', 'max:100'],
            'centro_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'centro_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'radio_km' => ['nullable', 'integer', 'min:1', 'max:500'],
            'poligono' => ['nullable', 'array', 'min:3'],
            'poligono.*.lat' => ['required_with:poligono', 'numeric', 'between:-90,90'],
            'poligono.*.lng' => ['required_with:poligono', 'numeric', 'between:-180,180'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'estado' => ['required', Rule::in(['activa', 'inactiva'])],
            'prioridad' => ['required', 'integer', 'between:1,5'],
            'notas' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la zona es obligatorio.',
            'nombre.unique' => 'Ya existe una zona con este nombre.',
            'codigo.unique' => 'Ya existe una zona con este código.',
            'centro_lat.between' => 'La latitud debe estar entre -90 y 90.',
            'centro_lng.between' => 'La longitud debe estar entre -180 y 180.',
            'radio_km.min' => 'El radio debe ser al menos 1 km.',
            'poligono.min' => 'El polígono debe tener al menos 3 puntos.',
            'color.regex' => 'El color debe ser un código hexadecimal válido (ej: #3498db).',
            'prioridad.between' => 'La prioridad debe estar entre 1 y 5.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convertir ciudades de string a array si viene separado por comas
        if ($this->has('ciudades') && is_string($this->ciudades)) {
            $this->merge([
                'ciudades' => array_filter(array_map('trim', explode(',', $this->ciudades)))
            ]);
        }

        // Convertir polígono de JSON string a array si es necesario
        if ($this->has('poligono') && is_string($this->poligono)) {
            $this->merge([
                'poligono' => json_decode($this->poligono, true)
            ]);
        }
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
// UpdateZonaRequest
// ═══════════════════════════════════════════════════════════════════════════════
class UpdateZonaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajustar según policies
    }

    public function rules(): array
    {
        $zonaId = $this->route('zona')->id ?? $this->route('zona');

        return [
            'nombre' => ['required', 'string', 'max:100', Rule::unique('zonas', 'nombre')->ignore($zonaId)],
            'codigo' => ['nullable', 'string', 'max:20', Rule::unique('zonas', 'codigo')->ignore($zonaId)],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'ciudades' => ['nullable', 'array'],
            'ciudades.*' => ['string', 'max:100'],
            'centro_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'centro_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'radio_km' => ['nullable', 'integer', 'min:1', 'max:500'],
            'poligono' => ['nullable', 'array', 'min:3'],
            'poligono.*.lat' => ['required_with:poligono', 'numeric', 'between:-90,90'],
            'poligono.*.lng' => ['required_with:poligono', 'numeric', 'between:-180,180'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'estado' => ['required', Rule::in(['activa', 'inactiva'])],
            'prioridad' => ['required', 'integer', 'between:1,5'],
            'notas' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la zona es obligatorio.',
            'nombre.unique' => 'Ya existe una zona con este nombre.',
            'codigo.unique' => 'Ya existe una zona con este código.',
            'centro_lat.between' => 'La latitud debe estar entre -90 y 90.',
            'centro_lng.between' => 'La longitud debe estar entre -180 y 180.',
            'radio_km.min' => 'El radio debe ser al menos 1 km.',
            'poligono.min' => 'El polígono debe tener al menos 3 puntos.',
            'color.regex' => 'El color debe ser un código hexadecimal válido (ej: #3498db).',
            'prioridad.between' => 'La prioridad debe estar entre 1 y 5.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('ciudades') && is_string($this->ciudades)) {
            $this->merge([
                'ciudades' => array_filter(array_map('trim', explode(',', $this->ciudades)))
            ]);
        }

        if ($this->has('poligono') && is_string($this->poligono)) {
            $this->merge([
                'poligono' => json_decode($this->poligono, true)
            ]);
        }
    }
}
