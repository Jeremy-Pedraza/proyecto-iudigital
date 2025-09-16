<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Reinicia el modelo interno.
     */
    public function reset(): void
    {
        $this->model = new User();
    }

    /**
     * Busca un usuario por ID.
     */
    public function findById(int $id): User
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Busca un usuario por username.
     */
    public function findByUsername(string $username): ?User
    {
        return $this->model->where('username', $username)->first();
    }

    /**
     * Crea un nuevo usuario.
     */
    public function create(array $data): User
    {
        // Hashear password antes de guardar
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->model->create($data);
    }

    /**
     * Actualiza un usuario.
     */
    public function update(int $id, array $data): User
    {
        $user = $this->model->findOrFail($id);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user;
    }

    /**
     * Elimina un usuario.
     */
    public function delete(int $id): void
    {
        $user = $this->model->findOrFail($id);
        $user->delete();
    }

    /**
     * Obtiene todos los usuarios.
     */
    public function all()
    {
        return $this->model->all();
    }
}
