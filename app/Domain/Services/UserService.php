<?php

namespace App\Domain\Services;

use App\Domain\Contracts\Users\UserRepositoryInterface;
use App\Domain\Contracts\Users\UserServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $repo
    ) {}

    public function list(?string $query, ?string $role, ?string $status, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->paginateFiltered($query, $role, $status, $perPage);
    }

    public function getOne(string $by, int|string $value, array $with = []): User
    {
        return $this->repo->findBy($by, $value, $with);
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {

            // Normalizaciones mínimas
            if (!empty($data['password']) && !str_starts_with($data['password'], '$2y$')) {
                $data['password'] = Hash::make($data['password']);
            }

            if (!array_key_exists('is_active', $data)) {
                $data['is_active'] = true;
            }

            // Validación simple (opcional, el Controller ya valida)
            if (empty($data['email'])) {
                throw ValidationException::withMessages(['email' => 'Email requerido']);
            }

            $user = $this->repo->create($data);

            // Asignación de rol si lo envías desde el Controller (opcional aquí)
            // Mejor dejar la sincronización de roles en el Controller (como ya lo haces)
            return $user;
        });
    }

    public function update(int $id, array $changes): User
    {
        return DB::transaction(function () use ($id, $changes) {
            if (isset($changes['password']) && $changes['password']) {
                if (!str_starts_with($changes['password'], '$2y$')) {
                    $changes['password'] = Hash::make($changes['password']);
                }
            } else {
                // Evitar sobreescribir con null si no vino password
                unset($changes['password']);
            }

            return $this->repo->update($id, $changes);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $this->repo->delete($id);
        });
    }
}
