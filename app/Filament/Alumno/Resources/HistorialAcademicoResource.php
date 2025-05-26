<?php

namespace App\Filament\Alumno\Resources;

use App\Filament\Alumno\Resources\HistorialAcademicoResource\Pages;
use App\Filament\Alumno\Resources\HistorialAcademicoResource\RelationManagers;
use App\Models\Cursada;
use App\Models\HistorialAcademico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class HistorialAcademicoResource extends Resource
{
    protected static ?string $model = Cursada::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPanel(): string
    {
        return 'alumno';
    }

    public static function getModelLabel(): string
    {
        return 'Historial Académico';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Historial Académico';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Cursada::where('dni_alumno', Auth::user()->dni)->with(['materia', 'comisionMateria'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('materia.nombre')->label('Materia'),
                Tables\Columns\TextColumn::make('comisionMateria.nombre')->label('Comisión'),
                Tables\Columns\TextColumn::make('materia.anio')->label('Año')->sortable(),
                Tables\Columns\TextColumn::make('cuatrimestre')->label('Periodo'),
                Tables\Columns\TextColumn::make('estado')->badge(),
                Tables\Columns\TextColumn::make('nota_1erParcial')->label('1er Parcial'),
                Tables\Columns\TextColumn::make('nota_2doParcial')->label('2do Parcial'),
                Tables\Columns\TextColumn::make('nota_final')->label('Final'),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistorialAcademicos::route('/'),
        ];
    }
}
