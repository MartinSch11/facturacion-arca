<?php

namespace App\Filament\Resources\ComisionesResource\Pages;

use App\Filament\Resources\ComisionesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComisiones extends ListRecords
{
    protected static string $resource = ComisionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
