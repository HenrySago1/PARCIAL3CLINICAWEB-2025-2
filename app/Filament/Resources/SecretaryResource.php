<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SecretaryResource\Pages;
use App\Models\Secretary;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class SecretaryResource extends Resource
{
    protected static ?string $model = Secretary::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Administrativos';
    protected static ?string $navigationLabel = 'Secretarias';
    protected static ?string $modelLabel = 'Secretaria';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Credenciales de Acceso')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255)
                            // ->dehydrated(false) // <-- ¡ELIMINADO!
                            ->visibleOn('create'),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required()
                            ->rule(Password::min(8))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            // ->dehydrated(false) // <-- ¡ELIMINADO!
                            ->visibleOn('create'),
                    ])->columns(2),

                Forms\Components\Section::make('Datos Personales')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required(),
                        
                        Forms\Components\TextInput::make('ci')
                            ->label('Carnet de Identidad')
                            ->required()
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('ci')->label('CI'),
                Tables\Columns\TextColumn::make('phone')->label('Teléfono'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSecretaries::route('/'),
            'create' => Pages\CreateSecretary::route('/create'),
            'edit' => Pages\EditSecretary::route('/{record}/edit'),
        ];
    }

    // Permisos (con la corrección visual para VSCode)
    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasRole('admin');
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasRole('admin');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasRole('admin');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasRole('admin');
    }
}