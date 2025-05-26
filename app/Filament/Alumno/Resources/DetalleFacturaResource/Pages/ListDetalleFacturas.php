<?php

namespace App\Filament\Alumno\Resources\DetalleFacturaResource\Pages;

use App\Filament\Alumno\Resources\DetalleFacturaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDetalleFacturas extends ListRecords
{
    protected static string $resource = DetalleFacturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
