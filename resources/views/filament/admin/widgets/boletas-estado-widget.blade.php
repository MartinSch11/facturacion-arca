{{-- filepath: c:\Users\Sistemas\facturacion-arca\resources\views\filament\admin\widgets\boletas-estado-widget.blade.php --}}
<x-filament::widget>
    <x-filament::card>
        <div style="display: flex; gap: 2rem;">
            <div>
                <div style="font-size: 2rem; font-weight: bold;">{{ $pagadas }}</div>
                <div>Boletas Pagadas</div>
            </div>
            <div>
                <div style="font-size: 2rem; font-weight: bold;">{{ $pendientes }}</div>
                <div>Boletas Pendientes</div>
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>