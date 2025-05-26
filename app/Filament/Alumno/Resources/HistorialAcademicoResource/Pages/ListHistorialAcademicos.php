<?php

namespace App\Filament\Alumno\Resources\HistorialAcademicoResource\Pages;

use App\Filament\Alumno\Resources\HistorialAcademicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistorialAcademicos extends ListRecords
{
    protected static string $resource = HistorialAcademicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
           //Actions\CreateAction::make(),
        ];
    }
}
