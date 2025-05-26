<?php

namespace App\Filament\Profesor\Resources\CursadaResource\Pages;

use App\Filament\Profesor\Resources\CursadaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCursada extends EditRecord
{
    protected static string $resource = CursadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Editar comisión: ' . $this->record->nombre;
    }

}
