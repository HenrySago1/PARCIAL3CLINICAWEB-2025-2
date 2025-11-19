<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Gestión Financiera';
    protected static ?string $navigationLabel = 'Pagos';
    protected static ?string $modelLabel = 'Pago';
    protected static ?int $sort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->searchable()
                    ->required()
                    ->label('Paciente'),
                
                Forms\Components\Select::make('service_id')
                    ->label('Servicio')
                    ->options(Service::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->reactive() // ¡IMPORTANTE! Reacciona al cambio
                    ->afterStateUpdated(fn ($state, callable $set) => 
                        $set('amount', Service::find($state)?->price ?? 0)
                    ),

                Forms\Components\TextInput::make('amount')
                    ->label('Total a Pagar (Bs)')
                    ->required()
                    ->numeric()
                    ->readOnly(), // Se llena solo

                Forms\Components\Select::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'efectivo' => 'Efectivo',
                        'qr' => 'QR',
                        'tarjeta' => 'Tarjeta',
                    ])
                    ->required()
                    ->default('efectivo'),
                
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pagado' => 'Pagado',
                        'pendiente' => 'Pendiente',
                    ])
                    ->required()
                    ->default('pagado'),

                Forms\Components\DatePicker::make('payment_date')
                    ->label('Fecha')
                    ->default(now())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')->label('Paciente')->searchable(),
                Tables\Columns\TextColumn::make('service.name')->label('Servicio'),
                Tables\Columns\TextColumn::make('amount')->money('bob')->label('Monto'),
                Tables\Columns\TextColumn::make('payment_date')->date('d/m/Y')->label('Fecha'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pagado' => 'success',
                        'pendiente' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->actions([
                // Botón de Imprimir
                Tables\Actions\Action::make('pdf')
                    ->label('Recibo')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Payment $record) => route('payment.pdf', $record))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasRole(['admin', 'secretary']);
    }
}