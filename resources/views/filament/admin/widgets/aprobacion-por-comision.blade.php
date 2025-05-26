<x-filament::widget>
    <x-filament::card>
        <h2 class="text-xl font-bold mb-4">Aprobación por Comisión</h2>

        <table class="w-full text-sm">
            <thead class="text-left text-gray-500 uppercase border-b">
                <tr>
                    <th class="py-2">Comisión</th>
                    <th class="py-2">Aprobación (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($comisiones as $comision)
                    <tr>
                        <td class="py-2 font-medium">{{ $comision['nombre'] }}</td>
                        <td class="py-2">{{ $comision['porcentaje'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-filament::card>
</x-filament::widget>
