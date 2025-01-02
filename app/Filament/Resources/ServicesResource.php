<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicesResource\Pages;
use App\Filament\Resources\ServicesResource\RelationManagers;
use App\Helpers\CloudinaryHelper;
use App\Models\Doctor;
use App\Models\Services;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;

class ServicesResource extends Resource
{
    protected static ?string $model = Services::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535),
                Forms\Components\Select::make('doctor_id')
                    ->multiple()
                    ->relationship('doctors', 'doctor_id')
                    ->options(function () {
                        $doctors = Doctor::all();

                        foreach ($doctors as $doctor) {
                            $user = User::find($doctor->user_id);
                            $data[] = (object) ['id' => $doctor->id, 'name' => $user->name];
                        }
                        dd($data);

                        return $data;
                    })
                    ->label('Doctor')
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->required(),

                Forms\Components\TextInput::make('duration')
                    ->label('Duration (in minutes)')
                    ->numeric()
                    ->required(),
                Forms\Components\Repeater::make('benefits')
                    ->label('Benefits (one per line)')
                    ->schema([
                        Forms\Components\TextInput::make('benefits')
                            ->label('Benefit')
                            ->required(),
                    ])
                    ->columns(1) // Number of columns to display
                    ->required()
                    ->minItems(1) // Minimum number of inputs
                    ->maxItems(10), // Maximum number of inputs

                // Forms\Components\TextInput::make('benefits')
                //     // ->array()
                //     ->label('Benefits (one per line)'),
                Forms\Components\TextInput::make('discount_value')
                    ->numeric()
                    ->label('Discount Value'),
                Forms\Components\Select::make('discount_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed',
                    ])
                    ->label('Discount Type'),
                Forms\Components\TextInput::make('service_sale_tag')
                    ->label('Service Sale Tag')
                    ->maxLength(255),


                Forms\Components\FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->disk('cloudinary')
                    // ->directory('services/images')
                    // ->enableReordering()
                    ->maxSize(1024)
                    ->required()
                    ->saveUploadedFileUsing(function ($file) {
                        $secureUrl = CloudinaryHelper::upload($file, 'public');
                        return $secureUrl;
                    }),
                Forms\Components\FileUpload::make('video')
                    // ->disk('cloudinary')
                    ->disk('local') // Use a local disk temporarily
                    ->directory('temp_videos') // Optional temp directory
                    // ->acceptedFileTypes(['video/mp4', 'video/avi', 'video/mkv']) // Allowed video types
                    ->maxSize(10240) // Max size: 10MB
                    ->acceptedFileTypes(['video/*'])
                    ->saveUploadedFileUsing(function ($file) {
                        $secureUrl = CloudinaryHelper::uploadVideo($file, 'public');
                        return $secureUrl;
                    })->afterStateUpdated(function ($state) {
                        return "<video width='320' height='240' controls>
                                    <source src='{$state}' type='video/mp4'>
                                    Your browser does not support the video tag.
                                </video>";
                    }),

                Forms\Components\Toggle::make('home_based')
                    ->label('Home Based')
                    ->default(false),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('price')->sortable(),
                // TextColumn::make('discount_type')
                //     ->label('Discount Type')
                //     ->formatStateUsing(fn(string $state): string => ucfirst($state))
                //     ->color(fn(string $state): string => $state === 'percentage' ? 'success' : 'warning'),
                // BadgeColumn::make('discount_type')
                //     ->label('Discount Type')
                //     ->formatStateUsing(fn(string $state): string => ucfirst($state))
                //     ->colors([
                //         'success' => 'percentage',
                //         'warning' => 'fixed',
                //     ]),
                TextColumn::make('category.name')->label('Category')->sortable()->searchable(),
                ImageColumn::make('images')
                    ->label('Image')
                    ->disk('cloudinary')
                    ->width(50)
                    ->height(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateServices::route('/create'),
            'edit' => Pages\EditServices::route('/{record}/edit'),
            'show' => Pages\ShowServices::route('/{record}'),
        ];
    }
}
