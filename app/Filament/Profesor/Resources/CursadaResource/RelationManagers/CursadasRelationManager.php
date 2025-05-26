<?php
namespace App\Filament\Profesor\Resources\ComisionMateriaResource\RelationManagers;

use App\Models\ComisionMateria;
use App\Models\Cursada;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CursadasRelationManager extends RelationManager
{
    protected static string $relationship = 'cursadas';
    protected static ?string $recordTitleAttribute = 'nombre';

    protected function getTableHeading(): string
    {
        return 'Alumnos de la comisión: ' . $this->ownerRecord->nombre;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nota_1erParcial')->numeric()->label('Nota 1er Parcial'),
            Forms\Components\TextInput::make('nota_2doParcial')->numeric()->label('Nota 2do Parcial'),
            Forms\Components\TextInput::make('nota_final')->numeric()->label('Nota Final'),
            Forms\Components\Select::make('estado')
                ->options([
                    'Cursando' => 'Cursando',
                    'Aprobada' => 'Aprobada',
                    'Desaprobada' => 'Desaprobada',
                    'Abandonada' => 'Abandonada',
                ])
                ->required()
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('alumno.nombre')->label('Nombre'),
                Tables\Columns\TextColumn::make('alumno.apellido')->label('Apellido'),
                Tables\Columns\TextColumn::make('nota_1erParcial')
                    ->label('1° Parcial')
                    ->formatStateUsing(fn($state) => number_format((float)$state, 2, ',', ''))
                    ->sortable(),
                Tables\Columns\TextColumn::make('nota_2doParcial')
                    ->label('2° Parcial')
                    ->formatStateUsing(fn($state) => number_format((float)$state, 2, ',', ''))
                    ->sortable(),
                Tables\Columns\TextColumn::make('nota_final')
                    ->label('Final')
                    ->formatStateUsing(fn($state) => number_format((float)$state, 2, ',', ''))
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')->badge(),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make()
            ]);
    }
}

