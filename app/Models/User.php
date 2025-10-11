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
    'username',
    'lastname',
    'email',
    'password',
    'name',
    'is_active',
    'last_login_at',
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

  /**
   * @return BelongsToMany<\App\Models\Role>
   */
  public function roles(): BelongsToMany
  {
    return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
      ->withTimestamps();
  }

  public function hasRole(string $role): bool
  {
    $needle = mb_strtolower($role);

    // Si ya está cargada la relación, usa colección en memoria
    if ($this->relationLoaded('roles')) {
      return $this->roles
        ->pluck('name')
        ->map(fn($n) => mb_strtolower($n))
        ->contains($needle)
        ||
        $this->roles
        ->pluck('slug')
        ->map(fn($s) => mb_strtolower($s))
        ->contains($needle);
    }

    // Si no está cargada, consulta directa (más eficiente)
    return $this->roles()
      ->where(function ($q) use ($needle) {
        $q->where(DB::raw('LOWER(name)'), $needle)
          ->orWhere(DB::raw('LOWER(slug)'), $needle);
      })
      ->exists();
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

  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  public function getRoleNamesAttribute(): array
  {
    // Evita fallback a columna eliminada; solo relación
    $roles = $this->relationLoaded('roles') ? $this->roles : $this->roles()->get();
    return $roles->isNotEmpty()
      ? $roles->pluck('name')->values()->all()
      : [];
  }
}
