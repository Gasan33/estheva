<?php

namespace App\Filament\Resources\DoctorsResource\Pages;

use App\Filament\Resources\DoctorsResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateDoctors extends CreateRecord
{
    protected static string $resource = DoctorsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove user fields from doctor data
        $user = User::create([
            'name' => $data['first_name'] . $data['last_name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => Hash::make('doctor'),
            'role' => 'doctor',
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'profile_picture' => $data['profile_picture'],
        ]);

        $data['user_id'] = $user->id;


        unset($data['user_id'], $data['first_name'], $data['last_name'], $data['email'], $data['phone_number'], $data['date_of_birth'], $data['gender'], $data['profile_picture']);
        // $user = User::create($this->userData);
        return $data;
    }

    protected function afterCreate(): void
    {

        $doctor = $this->record;
        DB::transaction(function () use ($doctor) {
            // Create the user
            $user = User::find($this->data['user_id']);

            // Link the doctor to the newly created user
            $doctor->update(['user_id' => $user->id]);
        });
    }
    // protected function handleRecordCreation(array $data): Doctor
    // {
    //     return DB::transaction(function () use ($data) {
    //         // Create the user
    //         $user = User::create(session()->get('userData'));

    //         // Create the doctor and link the user_id
    //         $data['user_id'] = $user->id;

    //         // Create the doctor record
    //         return Doctor::create($data);
    //     });
    // }

    // public static function create(array $data)
    // {
    //     dd($data);
    //     // Start a database transaction
    //     DB::transaction(function () use ($data) {
    //         // Create the user
    //         $user = User::create([
    //             'name' => $data['user']['first_name'] . $data['user']['last_name'],
    //             'first_name' => $data['user']['first_name'],
    //             'last_name' => $data['user']['last_name'],
    //             'email' => $data['user']['email'],
    //             'phone_number' => $data['user']['phone_number'],
    //             'password' => Hash::make('doctor'),
    //             'role' => 'doctor',
    //             'date_of_birth' => $data['user']['date_of_birth'],
    //             'gender' => $data['user']['gender'],
    //             'profile_picture' => $data['user']['profile_picture'],
    //         ]);

    //         // Create the doctor, linking the user_id
    //         Doctor::create([
    //             'user_id' => $user->id, // Set the user_id to the newly created user
    //             'specialty' => $data['doctor']['specialty'],
    //             'certificate' => $data['doctor']['certificate'],
    //             'university' => $data['doctor']['university'],
    //             'patients' => $data['doctor']['patients'],
    //             'exp' => $data['doctor']['exp'],
    //             'about' => $data['doctor']['about'],
    //             'home_based' => $data['doctor']['home_based'],
    //             'day_of_week' => $data['doctor']['day_of_week'],
    //             'start_time' => $data['doctor']['start_time'],
    //             'end_time' => $data['doctor']['end_time'],
    //             // Add other doctor fields
    //         ]);
    //     });
    // }
}
