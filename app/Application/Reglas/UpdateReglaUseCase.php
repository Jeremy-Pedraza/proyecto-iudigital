<?php

namespace App\Application\Reglas;

use App\Domain\Contracts\Reglas\ReglaServiceInterface;
use App\Models\Regla;

class UpdateReglaUseCase
{
    public function __construct(private ReglaServiceInterface $service) {}

    /**
     * Ejecuta el caso de uso
     * 
     * @param Regla $regla
     * @param array $data
     * @return Regla
     * @throws \DomainException Si la regla no es editable
     * @throws \InvalidArgumentException Si el valor no es válido
     */
    public function __invoke(Regla $regla, array $data): Regla
    {
        return $this->service->update($regla, $data);
    }
}
