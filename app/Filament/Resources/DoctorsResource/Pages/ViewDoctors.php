<?php

namespace App\Filament\Resources\DoctorsResource\Pages;

use App\Filament\Resources\DoctorsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDoctors extends ViewRecord
{
    protected static string $resource = DoctorsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
