<?php

namespace App\Filament\Pages;

use App\Models\Materia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class EditarCorrelativasMateria extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public ?Materia $materia = null;
    public array $data = [];

    protected static string $view = 'filament.pages.editar-correlativas-materia';

    public static function getSlug(): string
    {
        return 'editar-correlativas/{materia}';
    }

    public function getTitle(): string
    {
        return '';
    }

    public function mount(Materia $materia): void
    {
        $this->materia = $materia;

        $this->form->fill([
            'nombre_materia' => $materia->nombre,
            'correlativas' => $materia->correlativas()->pluck('materias.id')->toArray(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombre_materia')
                ->label('Materia')
                ->default($this->materia?->nombre)
                ->disabled()
                ->dehydrated(false)
                ->required(),

            Forms\Components\Select::make('correlativas')
                ->label('Correlativas')
                ->multiple()
                ->options(
                    Materia::where('id_plan_estudio', $this->materia->id_plan_estudio)
                        ->where('id', '!=', $this->materia->id)
                        ->where('anio', '<=', $this->materia->anio)
                        ->pluck('nombre', 'id')
                )
                ->required()

        ])->columns(2)->statePath('data');
    }

    public function save()
    {
        $idsSeleccionados = collect($this->form->getState()['correlativas'] ?? []);

        $idsValidos = Materia::where('id_plan_estudio', $this->materia->id_plan_estudio)
            ->where('anio', '<=', $this->materia->anio)
            ->whereIn('id', $idsSeleccionados)
            ->pluck('id');

        $this->materia->correlativas()->sync($idsValidos);

        \Filament\Notifications\Notification::make()
            ->title('Correlativas actualizadas')
            ->success()
            ->send();
    }
}
