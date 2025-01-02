<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorsResource\Pages;
use App\Filament\Resources\DoctorsResource\RelationManagers;
use App\Models\Doctor;
use App\Models\Doctors;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorsResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Forms\Components\TextInput::make('specialty')
                    ->label('Specialty')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('certificate')
                    ->label('Certificate')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('university')
                    ->label('University')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('patients')
                    ->label('Patients')
                    ->numeric()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('exp')
                    ->label('exp')
                    ->numeric()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('about')
                    ->label('About')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TimePicker::make('start_time')
                    ->label('Start Time')
                    ->required(),

                // End time field
                Forms\Components\TimePicker::make('end_time')
                    ->label('End Time')
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('user.profile_picture_url') // Use the accessor defined in the model
                    ->label('Profile Picture')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('user.name')->label('Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('specialty')
                    ->searchable(),
                Tables\Columns\TextColumn::make('exp')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctors::route('/create'),
            'view' => Pages\ViewDoctors::route('/{record}'),
            'edit' => Pages\EditDoctors::route('/{record}/edit'),
        ];
    }
}
