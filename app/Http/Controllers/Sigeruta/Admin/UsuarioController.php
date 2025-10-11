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
        private readonly ListUsersUseCase $listUsers,
        private readonly GetUserUseCase $getUser,
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
        $q = trim($request->string('q'));
        $role = $request->string('role');
        $status = $request->string('status');
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

        return view('admin.users.index', compact('users', 'q', 'role', 'roles', 'status', 'perPage'));
    }

    /**
     * GET /admin/usuarios/create
     * Formulario de creación
     */
    public function create()
    {
        $user = new User();

        $roles = class_exists(Role::class)
            ? Role::query()->orderBy('name')->pluck('name', 'name')
            : collect(['Admin' => 'Admin', 'Supervisor' => 'Supervisor', 'Cobranzas' => 'Cobranzas']);

        return view('admin.users.create', compact('user', 'roles'));
    }

    /**
     * POST /admin/usuarios
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        // Validación completa
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'], // ← AGREGADO
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'], // ← AGREGADO (opcional pero recomendado)
            'is_active' => ['nullable', 'boolean'],
            'role' => ['nullable', 'string', 'max:64'],
        ], [
            // Mensajes personalizados (opcional)
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'name.required' => 'El nombre es obligatorio.',
            'lastname.required' => 'Los apellidos son obligatorios.',
            'email.required' => 'El email es obligatorio.',
            'email.unique' => 'Este email ya está registrado.',
        ]);

        try {
            // Preparar datos para el caso de uso
            $data = [
                'name' => $validated['name'],
                'lastname' => $validated['lastname'], // ← AGREGADO
                'username' => $validated['username'] ?? null,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => (bool) ($validated['is_active'] ?? true),
                'email_verified_at' => null,
            ];

            // Crear usuario vía caso de uso
            /** @var User $user */
            $user = $this->createUser->handle($data);

            // ✅ ASIGNAR ROL - MÉTODO CORRECTO PARA TU IMPLEMENTACIÓN
            if (!empty($validated['role'])) {
                // Buscar el rol por nombre o slug
                $role = Role::where('name', $validated['role'])
                    ->orWhere('slug', $validated['role'])
                    ->first();

                if ($role) {
                    // Usar el método sync de Laravel (no syncRoles de Spatie)
                    $user->roles()->sync([$role->id]);

                    Log::info('Rol asignado', [
                        'user_id' => $user->id,
                        'role_id' => $role->id,
                        'role_name' => $role->name
                    ]);
                } else {
                    Log::warning('Rol no encontrado', ['role' => $validated['role']]);
                }
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Usuario creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear usuario', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['general' => 'Ocurrió un error al crear el usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * GET /admin/usuarios/{usuario}
     * Ver detalle
     * Route-Model Binding: {usuario} -> User $usuario
     */
    public function show(User $user)
    {
        // Si necesitas cargar relaciones (roles, etc.)
        if (class_exists(Role::class)) {
            $user->loadMissing('roles');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * GET /admin/usuarios/{usuario}/edit
     * Formulario de edición
     */
    public function edit(User $user)
    {
        if (class_exists(Role::class)) {
            $user->loadMissing('roles');
        }

        $roles = class_exists(Role::class)
            ? Role::query()->orderBy('name')->pluck('name', 'name')
            : collect(['Admin' => 'Admin', 'Supervisor' => 'Supervisor', 'Cobranzas' => 'Cobranzas']);

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * PUT/PATCH /admin/usuarios/{usuario}
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        // Validación
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'role' => ['nullable', 'string', 'max:64'],
        ]);

        try {
            $changes = [
                'name' => $validated['name'],
                'username' => $validated['username'] ?? $user->username,
                'email' => $validated['email'],
                'is_active' => (bool) ($validated['is_active'] ?? $user->is_active),
            ];

            if (!empty($validated['password'])) {
                $changes['password'] = Hash::make($validated['password']);
            }

            // Actualizar vía caso de uso
            /** @var User $user */
            $user = $this->updateUser->handle($user->id, $changes);

            // ✅ ACTUALIZAR ROL - MÉTODO CORRECTO
            if (array_key_exists('role', $validated)) {
                if (!empty($validated['role'])) {
                    // Buscar el rol por nombre o slug
                    $role = Role::where('name', $validated['role'])
                        ->orWhere('slug', $validated['role'])
                        ->first();

                    if ($role) {
                        // sync() reemplaza todos los roles con el nuevo
                        $user->roles()->sync([$role->id]);

                        Log::info('Rol actualizado', [
                            'user_id' => $user->id,
                            'role_id' => $role->id,
                            'role_name' => $role->name
                        ]);
                    }
                } else {
                    // Sin rol: eliminar todos los roles
                    $user->roles()->detach();

                    Log::info('Roles removidos', ['user_id' => $user->id]);
                }
            }

            return redirect()
                ->route('admin.users.edit', $user)
                ->with('success', 'Usuario actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar usuario', ['id' => $user->id, 'ex' => $e]);
            return back()->withInput()->withErrors(['general' => 'Ocurrió un error al actualizar el usuario.']);
        }
    }

    /**
     * DELETE /admin/usuarios/{usuario}
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        try {
            $this->deleteUser->handle($user->id);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Usuario eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar usuario', ['id' => $user->id, 'ex' => $e]);

            // Si hay FK o restricciones, podrías optar por soft-delete o is_active=false
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['general' => 'No se pudo eliminar el usuario. Verifica dependencias o intenta desactivarlo.']);
        }
    }
}
