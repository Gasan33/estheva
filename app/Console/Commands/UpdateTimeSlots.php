<?php

namespace App\Console\Commands;

use App\Models\Doctor;
use App\Models\services;
use Illuminate\Console\Command;
use App\Services\TimeSlotService;
use Carbon\Carbon;

class UpdateTimeSlots extends Command
{
    // The name and signature of the console command
    protected $signature = 'timeslots:update {service_name}';

    // The console command description
    protected $description = 'Automatically update time slots for the next day based on service duration';

    public function __construct()
    {
        parent::__construct();
    }

    // The logic to generate time slots for the next day
    public function handle()
    {
        // Get the service name from the command argument
        $serviceName = $this->argument('service_name');

        // Retrieve the service by name
        $service = services::where('name', $serviceName)->first();

        if (!$service) {
            $this->error('Service not found.');
            return;
        }

        // Get the slot duration for this service
        $slotDuration = $service->duration;
        $doctor_id = $service->doctor_id;
        $doctor = Doctor::find($doctor_id);
        if (!$doctor) {
            $this->error('doctor not found.');
            return;
        }

        // Get the date for the next day
        $date = Carbon::tomorrow()->toDateString();
        $startTime = $doctor->start_time; // Start time (you can customize this)
        $endTime = $doctor->end_time;  // End time (you can customize this)

        // Generate the time slots for the next day with the service's slot duration
        TimeSlotService::generateSlots($doctor_id, $service->id, $date, $startTime, $endTime, $slotDuration);

        $this->info('Time slots updated successfully for ' . $date . ' with a slot duration of ' . $slotDuration . ' minutes for the service "' . $serviceName . '".');
    }
}
