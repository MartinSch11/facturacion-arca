<?php

namespace App\Filament\Resources\DetalleFacturaResource\Pages;

use App\Filament\Resources\DetalleFacturaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDetalleFactura extends EditRecord
{
    protected static string $resource = DetalleFacturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
