<?php

namespace App\Filament\Profesor\Resources\CursadaResource\Pages;

use App\Filament\Profesor\Resources\CursadaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCursadas extends ListRecords
{
    protected static string $resource = CursadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
