<?php

namespace App\Filament\Resources\AlumnoResource\Pages;

use App\Filament\Resources\AlumnoResource;
use App\Models\Alumnos;
use App\Models\CarreraXAlumno;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateAlumno extends CreateRecord
{
    protected static string $resource = AlumnoResource::class;

    protected function handleRecordCreation(array $data): Alumnos
    {
        // Verificar si el alumno ya existe
        $existingAlumno = Alumnos::find($data['dni']);
        if ($existingAlumno) {
            Notification::make()
                ->title('Error')
                ->body('El DNI ya está registrado.')
                ->danger()
                ->send();

            throw new \Exception('El DNI ya está registrado.');
        }

        // Verificar si ya existe un usuario con el DNI o email
        $existingUser = User::where('dni', $data['dni'])->orWhere('email', $data['email'])->first();
        if ($existingUser) {
            Notification::make()
                ->title('Error')
                ->body('El DNI o correo ya está asociado a un usuario.')
                ->danger()
                ->send();

            throw new \Exception('El DNI o correo ya está asociado a un usuario.');
        }

        // Crear el alumno
        $alumno = Alumnos::create([
            'dni' => $data['dni'],
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'cuit' => $data['cuit'],
            'direccion' => $data['direccion'],
        ]);

        // Generar matrícula y crear registro en CarreraXAlumno
        $matricula = CarreraXAlumno::generateMatricula($data['dni'], $data['id_carrera']);
        CarreraXAlumno::create([
            'dni_alumno' => $data['dni'],
            'id_carrera' => $data['id_carrera'],
            'matricula' => $matricula,
            'id_condicion' => $data['id_condicion'],
        ]);

        // Crear un usuario para el alumno
        User::create([
            'name' => $data['nombre'] . ' ' . $data['apellido'],
            'email' => $data['email'],
            'password' => Hash::make('password'), // Contraseña predeterminada
            'dni' => $data['dni'],
        ]);

        return $alumno;
    }
}
