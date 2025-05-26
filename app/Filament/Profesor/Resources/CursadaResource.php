<?php

namespace App\Filament\Profesor\Resources;

use App\Filament\Profesor\Resources\ComisionMateriaResource\RelationManagers\CursadasRelationManager;
use App\Filament\Profesor\Resources\CursadaResource\Pages;
use App\Models\ComisionMateria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CursadaResource extends Resource
{
    protected static ?string $model = ComisionMateria::class;
    protected static ?string $navigationLabel = 'Notas de Alumnos';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getNavigationGroup(): ?string
    {
        return 'Gestión Académica';
    }

    public static function getModelLabel(): string
    {
        return 'Mis Comisiones';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mis Comisiones';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]); // No usamos formulario aquí
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Asegúrate de que Auth::user()->profesor_id tenga valor
                $dni = Auth::user()->dni ?? null;
                if ($dni) {
                    $query->whereHas('profesor', function ($q) use ($dni) {
                        $q->where('dni', $dni);
                    });
                } else {
                    $query->whereRaw('1=0');
                }

            })
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Comisión'),
                Tables\Columns\TextColumn::make('materia.nombre')->label('Materia'),
                Tables\Columns\TextColumn::make('division')->label('División'),
                Tables\Columns\TextColumn::make('turno')->label('Turno'),
                Tables\Columns\TextColumn::make('cursadas_count')
                    ->label('Inscriptos')
                    ->counts('cursadas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ver alumnos'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CursadasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCursadas::route('/'),
            'edit' => Pages\EditCursada::route('/{record}/edit'),
        ];
    }

}

