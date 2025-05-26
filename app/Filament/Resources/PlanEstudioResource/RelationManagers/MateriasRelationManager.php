<?php

namespace App\Filament\Resources\PlanEstudioResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MateriasRelationManager extends RelationManager
{
    protected static string $relationship = 'materias';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre'),
                Tables\Columns\TextColumn::make('anio')->label('Año'),
                Tables\Columns\TextColumn::make('modalidad')->label('Modalidad'),
            ]);
    }
}