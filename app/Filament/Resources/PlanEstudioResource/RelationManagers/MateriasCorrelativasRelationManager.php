<?php

namespace App\Filament\Resources\PlanEstudioResource\RelationManagers;

use App\Models\MateriasCorrelativas;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MateriasCorrelativasRelationManager extends RelationManager
{
    protected static string $relationship = 'materiasCorrelativas'; // Debes definir esta relación en tu modelo PlanEstudio

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('materia.nombre')->label('Materia'),
                Tables\Columns\TextColumn::make('correlativa.nombre')->label('Correlativa'),
                Tables\Columns\TextColumn::make('correlativa.planEstudio.carrera.nombre')
                    ->label('Carrera de la Correlativa'),
                Tables\Columns\TextColumn::make('correlativa.anio')
                    ->label('Año de la Correlativa'),
            ]);
    }
}