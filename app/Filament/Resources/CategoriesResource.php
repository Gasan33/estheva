<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriesResource\Pages;
use App\Filament\Resources\CategoriesResource\RelationManagers;
use App\Helpers\CloudinaryHelper;
use App\Models\Categories;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriesResource extends Resource
{
    protected static ?string $model = Categories::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('slug')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(5)
                    ->placeholder('Enter a detailed description...')
                    ->required(),

                Fieldset::make('Image')
                    ->relationship('image')
                    ->schema([
                        Forms\Components\FileUpload::make('path')
                            ->label('Category Picture')
                            ->image()
                            ->disk('cloudinary')
                            ->maxSize(1024)
                            ->visibility('public')
                            ->saveUploadedFileUsing(function ($file) {
                                $secureUrl = CloudinaryHelper::upload($file, 'public');
                                return $secureUrl;
                            }),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image.path')
                    ->label('Category Picture')
                    ->extraAttributes(['class' => 'rounded-full w-12 h-12']),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            // RelationManagers\ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategories::route('/create'),
            'edit' => Pages\EditCategories::route('/{record}/edit'),
        ];
    }
}
