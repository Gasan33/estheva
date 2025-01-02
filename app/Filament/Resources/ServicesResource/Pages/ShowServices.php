<?php

namespace App\Filament\Resources\ServicesResource\Pages;

use App\Filament\Resources\ServicesResource;
use App\Filament\Resources\ServicesResource\Widgets\ServiceDoctors;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;

class ShowServices extends ViewRecord
{
    protected static string $resource = ServicesResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ServiceDoctors::service($this->record),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Service Details')->schema([
                TextInput::make('name')->label('Name')->disabled(),
                Textarea::make('description')->label('Description')->disabled(),
                TextInput::make('price')->label('Price')->disabled(),
            ]),
            Forms\Components\Section::make('Media')->schema([
                Forms\Components\FileUpload::make('images')->label('Images')->disabled(),
                Forms\Components\FileUpload::make('video')->label('Video')->disabled(),
            ]),
            Forms\Components\Section::make('Discounts & Benefits')->schema([
                Forms\Components\TextInput::make('discount_value')->label('Discount Value')->disabled(),
                Forms\Components\TextInput::make('discount_type')->label('Discount Type')->disabled(),
                Forms\Components\Textarea::make('benefits')->label('Benefits')->disabled(),
            ]),
        ];
    }
}
