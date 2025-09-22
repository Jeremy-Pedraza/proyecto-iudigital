<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
  use HasFactory, Notifiable;

  protected $fillable = [
    'username',
    'email',
    'password',
    'name',
    'role',             // 'Admin' | 'Supervisor' | 'Cobranzas'
    'is_active',        // bool
    'last_login_at',    // timestamp
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

  public function roles()
  {
    return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
  }
  // ——— helpers mínimos que pediste ———
  public function hasRole(string $role): bool
  {
    // si hay relación, valida contra ella; si no, usa la columna 'role'
    if ($this->relationLoaded('roles') ? $this->roles->isNotEmpty() : $this->roles()->exists()) {
      $needle = mb_strtolower($role);
      return ($this->relationLoaded('roles') ? $this->roles : $this->roles()->get())
        ->contains(fn($r) => in_array(mb_strtolower($r->name), [$needle], true));
    }
    return strcasecmp((string) $this->role, $role) === 0;
  }

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

  // útil para filtros
  public function scopeRole($query, string $role)
  {
    return $query->whereRaw('LOWER(role) = ?', [mb_strtolower($role)]);
  }

  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  public function getRoleNamesAttribute(): array
  {
    if ($this->relationLoaded('roles') ? $this->roles->isNotEmpty() : $this->roles()->exists()) {
      return ($this->relationLoaded('roles') ? $this->roles : $this->roles()->get())
        ->pluck('name')
        ->values()
        ->all();
    }

    // fallback a la columna simple
    return $this->role ? [$this->role] : [];
  }
}
