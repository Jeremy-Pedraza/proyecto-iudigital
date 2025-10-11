<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
  use HasFactory, Notifiable;

  protected $fillable = [
    'name',
    'lastname',
    'username',
    'email',
    'password',
    'is_active',
    'last_login_at',
    'email_verified_at',
  ];

  protected $hidden = ['password', 'remember_token'];

  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password'          => 'hashed',
      'last_login_at'     => 'datetime',
      'is_active'         => 'boolean',
    ];
  }

  protected $appends = ['role_names'];

  // ═══════════════════════════════════════════════════════════════
  // RELACIONES
  // ═══════════════════════════════════════════════════════════════

  /**
   * Relación muchos a muchos con roles
   */
  public function roles(): BelongsToMany
  {
    return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
      ->withTimestamps();
  }

  // ═══════════════════════════════════════════════════════════════
  // MÉTODOS AUXILIARES PARA ROLES (estilo Spatie)
  // ═══════════════════════════════════════════════════════════════

  /**
   * Asignar uno o múltiples roles (reemplaza todos los existentes)
   * Uso: $user->assignRole('Admin')
   * Uso: $user->assignRole(['Admin', 'Supervisor'])
   */
  public function assignRole(string|array $roles): self
  {
    $roleIds = $this->getRoleIds($roles);
    $this->roles()->sync($roleIds);

    return $this;
  }

  /**
   * Agregar rol(es) sin eliminar los existentes
   * Uso: $user->giveRole('Admin')
   */
  public function giveRole(string|array $roles): self
  {
    $roleIds = $this->getRoleIds($roles);
    $this->roles()->syncWithoutDetaching($roleIds);

    return $this;
  }

  /**
   * Remover rol(es) específico(s)
   * Uso: $user->removeRole('Admin')
   */
  public function removeRole(string|array $roles): self
  {
    $roleIds = $this->getRoleIds($roles);
    $this->roles()->detach($roleIds);

    return $this;
  }

  /**
   * Verificar si el usuario tiene un rol específico
   * Uso: $user->hasRole('Admin')
   * Uso: $user->hasRole(['Admin', 'Supervisor']) // tiene alguno
   */
  public function hasRole(string|array $roles): bool
  {
    if (is_string($roles)) {
      $roles = [$roles];
    }

    $needle = array_map('mb_strtolower', $roles);

    // Si ya está cargada la relación, usa colección en memoria
    if ($this->relationLoaded('roles')) {
      return $this->roles->filter(function ($role) use ($needle) {
        return in_array(mb_strtolower($role->name), $needle)
          || in_array(mb_strtolower($role->slug), $needle);
      })->isNotEmpty();
    }

    // Si no está cargada, consulta directa
    return $this->roles()
      ->where(function ($q) use ($needle) {
        foreach ($needle as $role) {
          $q->orWhere(DB::raw('LOWER(name)'), $role)
            ->orWhere(DB::raw('LOWER(slug)'), $role);
        }
      })
      ->exists();
  }

  /**
   * Verificar si tiene TODOS los roles especificados
   * Uso: $user->hasAllRoles(['Admin', 'Supervisor'])
   */
  public function hasAllRoles(array $roles): bool
  {
    foreach ($roles as $role) {
      if (!$this->hasRole($role)) {
        return false;
      }
    }
    return true;
  }

  /**
   * Verificar si tiene AL MENOS UNO de los roles especificados
   * Uso: $user->hasAnyRole(['Admin', 'Supervisor'])
   */
  public function hasAnyRole(array $roles): bool
  {
    return $this->hasRole($roles);
  }

  /**
   * Obtener IDs de roles a partir de nombres o slugs
   */
  private function getRoleIds(string|array $roles): array
  {
    if (is_string($roles)) {
      $roles = [$roles];
    }

    return Role::where(function ($q) use ($roles) {
      foreach ($roles as $role) {
        $q->orWhere('name', $role)
          ->orWhere('slug', $role);
      }
    })->pluck('id')->toArray();
  }

  // ═══════════════════════════════════════════════════════════════
  // MÉTODOS AUXILIARES
  // ═══════════════════════════════════════════════════════════════

  public function isActive(): bool
  {
    return (bool) $this->is_active;
  }

  public function markLastLogin(): void
  {
    $this->forceFill(['last_login_at' => now()])->save();
  }

  public function getDisplayNameAttribute(): string
  {
    return $this->name ?: $this->username ?: $this->email;
  }

  public function getFullNameAttribute(): string
  {
    return trim($this->name . ' ' . $this->lastname);
  }

  // ═══════════════════════════════════════════════════════════════
  // SCOPES
  // ═══════════════════════════════════════════════════════════════

  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  public function scopeWithRole($query, string $role)
  {
    return $query->whereHas('roles', function ($q) use ($role) {
      $q->where('name', $role)->orWhere('slug', $role);
    });
  }

  // ═══════════════════════════════════════════════════════════════
  // ATRIBUTOS CALCULADOS
  // ═══════════════════════════════════════════════════════════════

  public function getRoleNamesAttribute(): array
  {
    $roles = $this->relationLoaded('roles') ? $this->roles : $this->roles()->get();
    return $roles->isNotEmpty()
      ? $roles->pluck('name')->values()->all()
      : [];
  }

  public function getFirstRoleAttribute(): ?Role
  {
    return $this->roles->first();
  }

  public function getFirstRoleNameAttribute(): ?string
  {
    return $this->roles->first()?->name;
  }
}
