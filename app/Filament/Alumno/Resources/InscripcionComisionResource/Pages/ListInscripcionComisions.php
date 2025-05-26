<?php

namespace App\Filament\Alumno\Resources\InscripcionComisionResource\Pages;

use App\Filament\Alumno\Resources\InscripcionComisionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInscripcionComisions extends ListRecords
{
    protected static string $resource = InscripcionComisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
