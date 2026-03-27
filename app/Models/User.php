<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\AccessControl;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'admin_id');
    }

    public function roleRecord(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function getRoleNameAttribute(): string
    {
        return $this->roleRecord?->name ?? $this->role ?? 'user';
    }

    public function hasRole(string $role): bool
    {
        return $this->role_name === $role;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->relationLoaded('roleRecord') && $this->roleRecord?->relationLoaded('permissions')) {
            return $this->roleRecord->permissions->contains('name', $permission);
        }

        if ($this->roleRecord) {
            return $this->roleRecord->permissions()->where('name', $permission)->exists();
        }

        return in_array($permission, AccessControl::permissionsForRole($this->role), true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
