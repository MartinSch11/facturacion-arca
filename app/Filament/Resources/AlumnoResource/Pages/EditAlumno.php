<?php

namespace App\Filament\Resources\AlumnoResource\Pages;

use App\Filament\Resources\AlumnoResource;
use App\Models\CarreraXAlumno;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditAlumno extends EditRecord
{
    protected static string $resource = AlumnoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Precarga la relación 'user' para que el campo email funcione.
     */
    public function getRecord(): Model
    {
        return parent::getRecord()->loadMissing('user');
    }

    /**
     * Rellena datos del formulario antes de mostrarlos.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Obtener carrera y condición del alumno
        $carreraXAlumno = CarreraXAlumno::where('dni_alumno', $data['dni'])->first();

        if ($carreraXAlumno) {
            $data['id_carrera'] = $carreraXAlumno->id_carrera;
            $data['id_condicion'] = $carreraXAlumno->id_condicion;
        }

        // Precargar email desde el usuario relacionado
        if ($this->record->user) {
            $data['email'] = $this->record->user->email;
        }

        return $data;
    }

    /**
     * Procesa datos del formulario antes de guardarlos.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Actualizar carrera y condición del alumno
        $carreraXAlumno = CarreraXAlumno::where('dni_alumno', $this->record->dni)->first();

        if ($carreraXAlumno) {
            $newCarreraId = $data['id_carrera'];
            $matricula = $carreraXAlumno->id_carrera != $newCarreraId
                ? CarreraXAlumno::generateMatricula($this->record->dni, $newCarreraId)
                : $carreraXAlumno->matricula;

            DB::table('CarreraXAlumno')
                ->where('dni_alumno', $this->record->dni)
                ->where('id_carrera', $carreraXAlumno->id_carrera)
                ->update([
                    'id_carrera' => $newCarreraId,
                    'id_condicion' => $data['id_condicion'],
                    'matricula' => $matricula,
                ]);
        }

        // Actualizar email del usuario
        if ($this->record->user && isset($data['email'])) {
            $this->record->user->update([
                'email' => $data['email'],
            ]);
        }

        // Evitar guardar campos que no pertenecen a 'Alumnos'
        unset($data['id_carrera'], $data['id_condicion'], $data['email']);

        return $data;
    }
}
