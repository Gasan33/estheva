<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->minLength(6)
                    ->maxLength(255)
                    ->required()
                    ->dehydrateStateUsing(fn($state) => $state ? Hash::make($state) : null)
                    ->label('Password'),
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'doctor' => 'Doctor',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('date_of_birth')
                    ->label('Date of Birth')
                    ->required(),
                Forms\Components\Radio::make('gender')
                    ->label('Gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('profile_picture')
                    ->label('Profile Picture')
                    ->image()
                    ->directory('profile_pictures') // Directory for storing profile pictures
                    ->nullable(),
                // Forms\Components\TextInput::make('first_name')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('last_name')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('email')
                //     ->email()
                //     ->required()
                //     ->unique(User::class, 'email', ignoreRecord: true),
                // Forms\Components\TextInput::make('phone_number')
                //     ->required()
                //     ->unique(User::class, 'phone_number', ignoreRecord: true),
                // Forms\Components\TextInput::make('password')
                //     ->password()
                //     ->minLength(6)
                //     ->maxLength(255)
                //     ->nullable()
                //     ->dehydrateStateUsing(fn($state) => $state ? Hash::make($state) : null)
                //     ->label('Password (Leave empty to keep current password)'),
                // Forms\Components\Select::make('role')
                //     ->required()
                //     ->options([
                //         'patient' => 'Patient',
                //         'doctor' => 'Doctor',
                //         'admin' => 'Admin',
                //     ]),
                // Forms\Components\Select::make('gender')
                //     ->required()
                //     ->options([
                //         'male' => 'Male',
                //         'female' => 'Female',
                //     ]),
                // Forms\Components\DatePicker::make('date_of_birth')
                //     ->nullable()
                //     ->label('Date of Birth'),
                // Forms\Components\FileUpload::make('profile_picture')
                //     ->label('Profile Picture')
                //     ->image()
                //     ->directory('profile_pictures') // Directory for storing profile pictures
                //     ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_picture_url') // Use the accessor defined in the model
                    ->label('Profile Picture')
                    ->circular()
                    ->size(50),
                Tables\Columns\ImageColumn::make('profile_picture_url') // Use the accessor for the profile picture
                    ->label('Profile Picture')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('first_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        $role = $record->role;
                        $colors = [
                            'patient' => 'success',
                            'doctor' => 'warning',
                            'admin' => 'danger',
                        ];
                        $color = $colors[$role] ?? 'secondary';
                        return '<span class="badge bg-' . $color . '">' . ucfirst($role) . '</span>';
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Gender'),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date('F j, Y')
                    ->label('Date of Birth'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'doctor' => 'Doctor',
                    ])
                    ->label('Filter by Role'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->mutateRecordDataUsing(function (array $data): array {
                    $data['user_id'] = auth('api')->id();

                    return $data;
                }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\AddressesRelationManager::class,
            // RelationManagers\MessagesRelationManager::class,
            // RelationManagers\MedicalReportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
