<?php

namespace App\Filament\Resources\ServicesResource\Widgets;

use Filament\Widgets\Widget;

class ServiceDoctors extends Widget
{
    protected static string $view = 'filament.resources.services-resource.widgets.service-doctors';


    public $service;

    public function mount($service)
    {
        $this->service = $service;
    }

    public function getDoctors()
    {
        return $this->service->doctors;
    }
}
