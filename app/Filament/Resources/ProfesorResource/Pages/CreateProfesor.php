<?php

namespace App\Filament\Resources\ProfesorResource\Pages;

use App\Filament\Resources\ProfesorResource;
use App\Models\Profesor;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateProfesor extends CreateRecord
{
    protected static string $resource = ProfesorResource::class;

    protected function handleRecordCreation(array $data): Profesor
    {
        if (User::where('dni', $data['dni'])->orWhere('email', $data['email'])->exists()) {
            Notification::make()->title('Error')->body('DNI o email ya registrado')->danger()->send();
            throw new \Exception('DNI o email ya registrado');
        }

        $profesor = Profesor::create($data);

        User::create([
            'name' => "{$data['nombre']} {$data['apellido']}",
            'email' => $data['email'],
            'dni' => $data['dni'],
            'password' => Hash::make('password'),
            'role' => 'profesor',
        ]);

        return $profesor;
    }
}
