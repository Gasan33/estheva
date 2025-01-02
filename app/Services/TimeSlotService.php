<?php

namespace App\Services;

use App\Models\TimeSlot;

class TimeSlotService
{
    public static function generateSlots($doctor_id = null, $service_id = null, $date, $startTime, $endTime, $slotDuration = 30, $availability = true)
    {
        // Convert start time and end time to carbon instances
        $start = \Carbon\Carbon::parse($date . ' ' . $startTime);
        $end = \Carbon\Carbon::parse($date . ' ' . $endTime);

        // Loop through the time range and create slots
        while ($start < $end) {
            $slotEndTime = $start->copy()->addMinutes($slotDuration);

            TimeSlot::create([
                'doctor_id' => $doctor_id,
                'service_id' => $service_id,
                'date' => $date,
                'start_time' => $start->format('H:i'),
                'end_time' => $slotEndTime->format('H:i'),
                'is_available' => $availability,
            ]);

            // Move to next slot
            $start = $slotEndTime;
        }
    }
}
