<x-filament::page>
    <h2 class="text-lg font-bold mb-4">Boleta n°{{ $boleta->nro_boleta }}</h2>
    <div class="">
        {{ $this->form }}
    </div>
    <x-filament::button wire:click="pagar" class="mt-4">Confirmar y Emitir Factura</x-filament::button>
</x-filament::page>
