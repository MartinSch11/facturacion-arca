<?php

namespace App\Filament\Resources\MateriasCorrelativasResource\Pages;

use App\Filament\Resources\MateriasCorrelativasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMateriasCorrelativas extends ListRecords
{
    protected static string $resource = MateriasCorrelativasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear'), // Puedes personalizar el label si quieres
        ];
    }
}
