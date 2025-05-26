<?php

namespace App\Filament\Alumno\Resources\PagoResource\Pages;

use App\Filament\Alumno\Resources\PagoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPago extends EditRecord
{
    protected static string $resource = PagoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
