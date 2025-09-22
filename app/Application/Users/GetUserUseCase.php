<?php
// app/Application/Users/GetUserUseCase.php

namespace App\Application\Users;

use App\Domain\Services\UserService;
use App\Models\User;

class GetUserUseCase
{
    public function __construct(
        private readonly UserService $service
    ) {}

    /**
     * Obtiene un usuario por ID (por defecto) o por otro identificador.
     *
     * @param  int|string  $value   Valor a buscar (id, email, username, etc.)
     * @param  array{
     *   by?: 'id'|'email'|'username',
     *   with?: string[]
     * }  $options
     *
     * @return User
     */
    public function handle(int|string $value, array $options = []): User
    {
        $by   = $options['by']   ?? 'id';     // 'id' | 'email' | 'username'
        $with = $options['with'] ?? [];       // relaciones a eager-load, ej: ['roles']

        return $this->service->getOne($by, $value, $with);
    }
}
