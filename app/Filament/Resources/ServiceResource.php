<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Gestión Financiera'; // Nuevo Grupo
    protected static ?string $navigationLabel = 'Servicios';
    protected static ?string $modelLabel = 'Servicio';
    protected static ?int $sort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nombre del Servicio'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Bs.')
                    ->label('Precio'),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Activo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->label('Servicio'),
                Tables\Columns\TextColumn::make('price')->money('bob')->label('Precio'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Activo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    // Permisos: Admin y Secretaria pueden gestionar
    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasRole(['admin', 'secretary']);
    }
    // (Puedes añadir canCreate, canEdit igual si quieres que la secretaria edite precios)
}