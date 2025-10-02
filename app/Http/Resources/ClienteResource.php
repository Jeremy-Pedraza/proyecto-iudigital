<?php

// app/Http/Resources/ClienteResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'razon_social' => $this->razon_social,
            'nombre_fantasia' => $this->nombre_fantasia,
            'documento' => $this->documento,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'coordenadas' => ['lat' => $this->lat, 'lng' => $this->lng],
            'frecuencia_visita' => $this->frecuencia_visita,
            'ventana_horaria' => $this->ventana_horaria,
            'prioridad' => $this->prioridad,
            'estado' => $this->estado,
            'comercial' => $this->whenLoaded('comercial', fn() => [
                'id' => $this->comercial->id,
                'nombre' => $this->comercial->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
