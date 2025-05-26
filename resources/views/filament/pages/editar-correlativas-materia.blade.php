<x-filament::page>
    <x-filament::breadcrumbs :breadcrumbs="[
        route('filament.admin.resources.materias.index') => 'Materias',
        route('filament.admin.resources.materias-correlativas.index') => 'Correlativas',
        url()->current() => 'Editando: ' . ($this->materia->nombre ?? 'Materia'),
    ]"/>

    <h1 class="text-3xl font-bold mb-2 p-0">Editar correlativas</h1>

    {{ $this->form }}

    <x-filament::actions alignment="left">
        <x-filament::button type="submit" form="form">
            Guardar
        </x-filament::button>
        <x-filament::button tag="a" href="{{ route('filament.admin.resources.materias-correlativas.index') }}"
            color="gray"
            class="rounded-lg border border-gray-300 bg-gray-100 text-gray-700 hover:bg-gray-500 hover:text-gray-800 shadow-sm">
            Cancelar
        </x-filament::button>
    </x-filament::actions>
</x-filament::page>
