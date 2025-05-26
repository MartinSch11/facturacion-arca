<?php

namespace App\Filament\Resources\ComisionesResource\Pages;

use App\Filament\Resources\ComisionesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComisiones extends EditRecord
{
    protected static string $resource = ComisionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
