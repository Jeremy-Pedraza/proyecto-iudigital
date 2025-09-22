<?php

namespace App\Http\Controllers\Sigeruta\Admin;

use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

// Casos de uso (capa de aplicación) – ajusta namespaces a tu proyecto
use App\Application\Users\ListUsersUseCase;
use App\Application\Users\GetUserUseCase;
use App\Application\Users\CreateUserUseCase;
use App\Application\Users\UpdateUserUseCase;
use App\Application\Users\DeleteUserUseCase;


class UsuarioController extends Controller
{
    public function __construct(
        private readonly ListUsersUseCase  $listUsers,
        private readonly GetUserUseCase    $getUser,
        private readonly CreateUserUseCase $createUser,
        private readonly UpdateUserUseCase $updateUser,
        private readonly DeleteUserUseCase $deleteUser,
    ) {}

    /**
     * GET /admin/usuarios
     * Listado con búsqueda y paginación
     */
    public function index(Request $request)
    {
        $q       = trim($request->string('q'));
        $role    = $request->string('role');
        $status  = $request->string('status');
        $perPage = (int) $request->integer('per_page', 10) ?: 10;

        $users = $this->listUsers->handle(
            query: $q,
            role: $role,
            status: $status,
            perPage: $perPage
        );

        // Obtener opciones de rol desde la BD si existe la tabla; si no, fallback
        if (Schema::hasTable('roles')) {
            $roles = Role::query()->orderBy('name')->pluck('name', 'name');
            if ($roles->isEmpty()) {
                $roles = collect(['Admin' => 'Admin', 'Supervisor' => 'Supervisor', 'Cobranzas' => 'Cobranzas']);
            }
        } else {
            $roles = collect(['Admin' => 'Admin', 'Supervisor' => 'Supervisor', 'Cobranzas' => 'Cobranzas']);
        }

        return view('admin.usuarios.index', compact('users', 'q', 'role', 'roles', 'status', 'perPage'));
    }

    /**
     * GET /admin/usuarios/create
     * Formulario de creación
     */
    public function create()
    {
        $usuario = new User();

        $roles = class_exists(Role::class)
            ? Role::query()->orderBy('name')->pluck('name', 'name')
            : collect(['Admin' => 'Admin', 'Supervisor' => 'Supervisor', 'Cobranzas' => 'Cobranzas']);

        return view('admin.usuarios.create', compact('usuario', 'roles'));
    }

    /**
     * POST /admin/usuarios
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'username'  => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            // Rol: si usas Spatie, debe existir en tabla roles; si no, valida enum manual
            'role'      => ['nullable', 'string', 'max:64'],
        ]);

        try {
            // DTO para caso de uso
            $data = [
                'name'        => $validated['name'],
                'username'    => $validated['username'] ?? null,
                'email'       => $validated['email'],
                'password'    => Hash::make($validated['password']),
                'is_active'   => (bool)($validated['is_active'] ?? true),
                'email_verified_at' => null,
            ];

            // Crear user vía caso de uso (que llama servicio → repo → modelo)
            /** @var User $user */
            $user = $this->createUser->handle($data);

            // Rol
            if (!empty($validated['role'])) {
                if (class_exists(Role::class)) {
                    // Spatie
                    $user->syncRoles([$validated['role']]);
                } else {
                    // Columna simple 'role'
                    $user->role = $validated['role'];
                    $user->save();
                }
            }

            return redirect()
                ->route('admin.usuarios.index')
                ->with('success', 'Usuario creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear usuario', ['ex' => $e]);
            return back()->withInput()->withErrors(['general' => 'Ocurrió un error al crear el usuario.']);
        }
    }

    /**
     * GET /admin/usuarios/{usuario}
     * Ver detalle
     * Route-Model Binding: {usuario} -> User $usuario
     */
    public function show(User $usuario)
    {
        // Si necesitas cargar relaciones (roles, etc.)
        if (class_exists(Role::class)) {
            $usuario->loadMissing('roles');
        }

        return view('admin.usuarios.show', compact('usuario'));
    }

    /**
     * GET /admin/usuarios/{usuario}/edit
     * Formulario de edición
     */
    public function edit(User $usuario)
    {
        if (class_exists(Role::class)) {
            $usuario->loadMissing('roles');
        }

        $roles = class_exists(Role::class)
            ? Role::query()->orderBy('name')->pluck('name', 'name')
            : collect(['Admin' => 'Admin', 'Supervisor' => 'Supervisor', 'Cobranzas' => 'Cobranzas']);

        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * PUT/PATCH /admin/usuarios/{usuario}
     * Actualizar usuario
     */
    public function update(Request $request, User $usuario)
    {
        // Validación
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'username'  => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($usuario->id)],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'role'      => ['nullable', 'string', 'max:64'],
        ]);

        try {
            $changes = [
                'name'      => $validated['name'],
                'username'  => $validated['username'] ?? $usuario->username,
                'email'     => $validated['email'],
                'is_active' => (bool)($validated['is_active'] ?? $usuario->is_active),
            ];

            if (!empty($validated['password'])) {
                $changes['password'] = Hash::make($validated['password']);
            }

            // Actualizar vía caso de uso
            /** @var User $user */
            $user = $this->updateUser->handle($usuario->id, $changes);

            // Rol
            if (array_key_exists('role', $validated)) {
                if (class_exists(Role::class)) {
                    if (!empty($validated['role'])) {
                        $user->syncRoles([$validated['role']]);
                    } else {
                        $user->syncRoles([]); // sin rol
                    }
                } else {
                    $user->role = $validated['role'] ?: null;
                    $user->save();
                }
            }

            return redirect()
                ->route('admin.usuarios.edit', $user)
                ->with('success', 'Usuario actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar usuario', ['id' => $usuario->id, 'ex' => $e]);
            return back()->withInput()->withErrors(['general' => 'Ocurrió un error al actualizar el usuario.']);
        }
    }

    /**
     * DELETE /admin/usuarios/{usuario}
     * Eliminar usuario
     */
    public function destroy(User $usuario)
    {
        try {
            $this->deleteUser->handle($usuario->id);

            return redirect()
                ->route('admin.usuarios.index')
                ->with('success', 'Usuario eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar usuario', ['id' => $usuario->id, 'ex' => $e]);

            // Si hay FK o restricciones, podrías optar por soft-delete o is_active=false
            return redirect()
                ->route('admin.usuarios.index')
                ->withErrors(['general' => 'No se pudo eliminar el usuario. Verifica dependencias o intenta desactivarlo.']);
        }
    }
}
