<?php

namespace App\Filament\Resources\PrecioResource\Pages;

use App\Filament\Resources\PrecioResource;
use App\Models\Concepto;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrecio extends CreateRecord
{
    protected static string $resource = PrecioResource::class;
}
