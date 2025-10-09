<?php

namespace App\Domain\Services;

use App\Domain\Contracts\Reglas\ReglaRepositoryInterface;
use App\Domain\Contracts\Reglas\ReglaServiceInterface;
use App\Models\Regla;
use Illuminate\Support\Collection;

class ReglaService implements ReglaServiceInterface
{
    public function __construct(private ReglaRepositoryInterface $repo) {}

    public function listGroupedByCategory(): Collection
    {
        return $this->repo->getAllGroupedByCategory();
    }

    public function list(): Collection
    {
        return $this->repo->getAll();
    }

    public function getByCategory(string $categoria): Collection
    {
        return $this->repo->getByCategory($categoria);
    }

    public function update(Regla $regla, array $data): Regla
    {
        // Validar que la regla sea editable
        if (!$regla->editable) {
            throw new \DomainException("Esta regla no puede ser modificada.");
        }

        // Validar el valor si es numérico
        if (isset($data['valor']) && !$regla->validarValor($data['valor'])) {
            throw new \InvalidArgumentException(
                "El valor debe estar entre {$regla->valor_minimo} y {$regla->valor_maximo}"
            );
        }

        return $this->repo->update($regla, $data);
    }

    public function getValorByCodigo(string $codigo)
    {
        $regla = $this->repo->findByCodigo($codigo);

        if (!$regla) {
            return null;
        }

        return $regla->valor_parseado;
    }

    public function validateValue(Regla $regla, $valor): bool
    {
        return $regla->validarValor($valor);
    }
}
