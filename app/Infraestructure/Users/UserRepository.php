<?php

namespace App\Infrastructure\Users;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Domain\Contracts\Users\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function paginateFiltered(?string $query, ?string $role, ?string $status, int $perPage = 10): LengthAwarePaginator
    {
        $q = User::query()
            ->when($query, function (Builder $b) use ($query) {
                $term = "%{$query}%";
                $b->where(function (Builder $w) use ($term) {
                    $w->where('name', 'like', $term)
                        ->orWhere('username', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when($status, function (Builder $b) use ($status) {
                if (in_array($status, ['active', 'inactive'], true)) {
                    $b->where('is_active', $status === 'active');
                } elseif ($status !== '') {
                    $b->where('is_active', (bool) $status);
                }
            })
            ->when($role, fn(Builder $b) => $b->where('role', $role));

        $paginator = $q->latest('id')->paginate($perPage);
        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
        $paginator->withQueryString(); // ✅ sin alerta

        return $paginator;
    }

    /**
     * Obtiene un usuario por id/email/username, con opción de eager load.
     *
     * @throws ModelNotFoundException
     */
    public function findBy(string $by, int|string $value, array $with = []): User
    {
        $allowed = ['id', 'email', 'username'];
        if (!in_array($by, $allowed, true)) {
            throw new \InvalidArgumentException("Campo de búsqueda no soportado: {$by}");
        }

        $builder = User::query();

        if (!empty($with)) {
            $builder->with($with);
        }

        $user = $builder->where($by, $value)->first();

        if (!$user) {
            throw (new ModelNotFoundException())->setModel(User::class, [$value]);
        }

        return $user;
    }

    /**
     * Crea un usuario (el servicio se encarga del hash si llega sin hashear).
     */
    public function create(array $data): User
    {
        /** @var User $user */
        $user = User::create($data);
        return $user;
    }

    /**
     * Actualiza un usuario por id y devuelve la instancia fresca.
     */
    public function update(int $id, array $changes): User
    {
        $user = User::findOrFail($id);
        $user->fill($changes);
        $user->save();

        return $user->fresh();
    }

    /**
     * Elimina un usuario por id.
     */
    public function delete(int $id): void
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
