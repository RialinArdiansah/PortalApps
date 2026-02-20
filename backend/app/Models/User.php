<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // --- Relationships ---

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'submitted_by_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'submitted_by_id');
    }

    // --- Helpers ---

    public function isAdmin(): bool
    {
        return in_array($this->role, ['Super admin', 'admin']);
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function canViewAll(): bool
    {
        return in_array($this->role, ['Super admin', 'admin', 'manager']);
    }
}
