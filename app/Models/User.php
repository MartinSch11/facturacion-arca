<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'dni',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación con Alumno
    public function alumno()
    {
        return $this->hasOne(Alumnos::class, 'dni', 'dni');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAlumno()
    {
        return $this->role === 'alumno';
    }

    public function isProfesor()
    {
        return $this->role === 'profesor';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin' && $this->isAdmin()) {
            return true;
        }
        if ($panel->getId() === 'alumno' && $this->isAlumno()) {
            return true;
        }
        if ($panel->getId() === 'profesor' && $this->isProfesor()) {
            return true;
        }
        return false;
    }
}
