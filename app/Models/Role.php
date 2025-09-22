<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Tabla y clave primaria (por si quieres personalizar)
    protected $table = 'roles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',         // Nombre visible: Admin, Supervisor, Cobranzas...
        'slug',         // admin, supervisor, cobranzas 
        'description',  // Texto descriptivo
        // 'is_active',  // <- opcional si quieres activar/desactivar roles
    ];

    protected $casts = [
        // 'is_active' => 'boolean', // <- descomenta si agregas la columna
    ];

    // ─────────────────────────────────────────────────────────────
    // Relaciones
    // ─────────────────────────────────────────────────────────────
    public function users()
    {
        // Pivote por convención: role_user (role_id, user_id)
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id')
            ->withTimestamps();
    }

    // ─────────────────────────────────────────────────────────────
    // Hooks: slug automático (simple y suficiente)
    // ─────────────────────────────────────────────────────────────
    protected static function booted(): void
    {
        static::saving(function (Role $role) {
            if (empty($role->slug) && !empty($role->name)) {
                $slug = str($role->name)->slug('-')->toString();

                // asegurar unicidad básica del slug
                $base = $slug;
                $i = 1;
                while (
                    static::where('slug', $slug)
                    ->when($role->exists, fn($q) => $q->whereKeyNot($role->getKey()))
                    ->exists()
                ) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }
                $role->slug = $slug;
            }
        });
    }

    // ─────────────────────────────────────────────────────────────
    // Scopes / helpers
    // ─────────────────────────────────────────────────────────────
    public function scopeSlug($query, string $slug)
    {
        return $query->whereRaw('LOWER(slug) = ?', [mb_strtolower($slug)]);
    }

    public function scopeActive($query)
    {
        // si agregas la columna is_active
        return $query->where('is_active', true);
    }

    public function assignTo(User $user): void
    {
        $this->users()->syncWithoutDetaching([$user->getKey()]);
    }

    public function revokeFrom(User $user): void
    {
        $this->users()->detach($user->getKey());
    }

    // Etiqueta amigable para UI
    public function getDisplayLabelAttribute(): string
    {
        return $this->name ?: $this->slug;
    }
}
