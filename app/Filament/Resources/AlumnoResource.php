<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlumnoResource\Pages;
use App\Models\Alumnos;
use App\Models\Carrera;
use App\Models\CarreraXAlumno;
use App\Models\Condicion;
use App\Models\Location;
use App\Models\Province;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlumnoResource extends Resource
{
    protected static ?string $model = Alumnos::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationItem(): NavigationItem
    {
        return NavigationItem::make(static::getNavigationLabel())
            ->url(static::getUrl())
            ->icon(static::getNavigationIcon())
            ->group(static::getNavigationGroup())
            ->sort(static::getNavigationSort());
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('dni')
                    ->required()
                    ->maxLength(20)
                    ->label('DNI')
                    ->unique(table: Alumnos::class, column: 'dni', ignorable: fn($record) => $record),

                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(50)
                    ->label('Nombre'),

                Forms\Components\TextInput::make('apellido')
                    ->required()
                    ->maxLength(50)
                    ->label('Apellido'),

                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->default(fn($record) => $record?->user?->email)
                    ->unique(
                        table: User::class,
                        column: 'email',
                        ignorable: fn($record) => $record?->user
                    )
                    ->dehydrated(false),


                Forms\Components\TextInput::make('cuit')
                    ->maxLength(20)
                    ->label('CUIT'),

                Forms\Components\TextInput::make('direccion')
                    ->maxLength(100)
                    ->label('Dirección'),

                Forms\Components\Select::make('province_id')
                    ->label('Provincia')
                    ->options(Province::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => $set('location_id', null)),

                Forms\Components\Select::make('location_id')
                    ->label('Localidad')
                    ->options(
                        fn(callable $get) =>
                        Location::where('province_id', $get('province_id'))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('id_carrera')
                    ->relationship('carreraXAlumnos', 'id_carrera')
                    ->options(Carrera::pluck('nombre', 'id_carrera')->toArray())
                    ->required()
                    ->label('Carrera'),

                Forms\Components\Select::make('id_condicion')
                    ->relationship('carreraXAlumnos', 'id_condicion')
                    ->options(Condicion::pluck('nombre', 'id_condicion')->toArray())
                    ->required()
                    ->label('Condición'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('apellido')->label('Apellido')->searchable(),
                Tables\Columns\TextColumn::make('dni')->label('DNI')->searchable(),
                Tables\Columns\TextColumn::make('carreraXAlumno.matricula')
                    ->label('Matrícula')
                    ->default('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Correo Electrónico')
                    ->default('-'),
                Tables\Columns\TextColumn::make('carreraXAlumno.carrera.nombre')
                    ->label('Carrera')
                    ->default('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('carreraXAlumno.condicion.nombre')
                    ->label('Condición')
                    ->default('-'),
                Tables\Columns\TextColumn::make('anio_carrera')
                    ->label('Año de Carrera')
                    ->getStateUsing(fn($record) => $record->anioCarreraActual()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('carrera')
                    ->label('Carrera')
                    ->options(
                        Carrera::pluck('nombre', 'id_carrera')->toArray()
                    )
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            return $query->whereHas('carreraXAlumno', function ($q) use ($data) {
                                $q->where('id_carrera', $data['value']);
                            });
                        }
                        return $query;
                    }),
                Tables\Filters\SelectFilter::make('condicion')
                    ->label('Condición')
                    ->options(
                        Condicion::pluck('nombre', 'id_condicion')->toArray()
                    )
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            return $query->whereHas('carreraXAlumno', function ($q) use ($data) {
                                $q->where('id_condicion', $data['value']);
                            });
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlumnos::route('/'),
            'create' => Pages\CreateAlumno::route('/create'),
            'edit' => Pages\EditAlumno::route('/{record}/edit'),
        ];
    }
}
