<?php

namespace App\Filament\Alumno\Resources\PagoResource\Pages;

use App\Filament\Alumno\Resources\PagoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPagos extends ListRecords
{
    protected static string $resource = PagoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
