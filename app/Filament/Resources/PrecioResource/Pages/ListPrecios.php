<?php

namespace App\Filament\Resources\PrecioResource\Pages;

use App\Filament\Resources\PrecioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrecios extends ListRecords
{
    protected static string $resource = PrecioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
