<x-filament::page>
    {{-- Bloque de bienvenida arriba --}}
    <div class="mb-8">
        @livewire(\Filament\Widgets\AccountWidget::class)
    </div>

    {{-- Gráficos más abajo, bien organizados --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @livewire(App\Filament\Widgets\GlaucomaVsNormalChart::class)
        @livewire(App\Filament\Widgets\TopDoctorsChart::class)
    </div>
</x-filament::page> 