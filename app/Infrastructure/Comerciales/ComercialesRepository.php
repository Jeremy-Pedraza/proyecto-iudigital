<?php

namespace App\Infrastructure\Comerciales;

use App\Domain\Contracts\Comerciales\ComercialesRepositoryInterface;
use App\Models\Comercial;

class ComercialesRepository implements ComercialesRepositoryInterface
{
    private $model;

    public function __construct(Comercial $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->orderBy('nombre', 'asc')->get();
    }

    public function findById(string $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'telefono' => $data['telefono'],
            'codigo_empleado' => $data['codigo_empleado'],
            'departamento' => $data['departamento'],
            'direccion' => $data['direccion'],
            'fecha_ingreso' => $data['fecha_ingreso'],
            'salario_base' => $data['salario_base'],
            'activo' => $data['activo'] ?? true,
        ]);
    }

    public function update(string $id, array $data)
    {
        $comercial = $this->findById($id);

        $comercial->update([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'telefono' => $data['telefono'],
            'codigo_empleado' => $data['codigo_empleado'],
            'departamento' => $data['departamento'],
            'direccion' => $data['direccion'],
            'fecha_ingreso' => $data['fecha_ingreso'],
            'salario_base' => $data['salario_base'],
            'activo' => $data['activo'] ?? true,
        ]);

        return $comercial->fresh();
    }

    public function delete(string $id)
    {
        $comercial = $this->findById($id);
        return $comercial->delete();
    }
}
