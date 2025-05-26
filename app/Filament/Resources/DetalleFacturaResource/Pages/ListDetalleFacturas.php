<?php

namespace App\Filament\Resources\DetalleFacturaResource\Pages;

use App\Filament\Resources\DetalleFacturaResource;
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
