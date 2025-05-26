<?php

namespace App\Filament\Resources\MateriasCorrelativasResource\Pages;

use App\Filament\Resources\MateriasCorrelativasResource;
use App\Models\MateriasCorrelativas;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMateriasCorrelativas extends CreateRecord
{
    protected static string $resource = MateriasCorrelativasResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
{
    // Evitamos crear directamente desde el método principal
    return [];
}

protected function afterCreate(): void
{
    foreach ($this->form->getState()['id_correlativas'] as $correlativaId) {
        MateriasCorrelativas::create([
            'id_materia' => $this->form->getState()['id_materia'],
            'id_correlativa' => $correlativaId,
        ]);
    }

    Notification::make()
        ->title('Correlatividades creadas correctamente.')
        ->success()
        ->send();

    $this->redirect(static::getResource()::getUrl('index'));
}

}
