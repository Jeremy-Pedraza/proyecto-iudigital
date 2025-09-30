<?php
// app/Http/Requests/ListasRapidas/UpdateListaRapidaRequest.php

namespace App\Http\Requests\ListasRapidas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListaRapidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'grupo' => ['required', 'string', 'max:100'],
            'clave' => ['required', 'string', 'max:100'],
            'valor' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ];
    }
}
