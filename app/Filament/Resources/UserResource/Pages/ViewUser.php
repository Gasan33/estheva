<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Redirect;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    protected static string $view = 'filament.resources.users.pages.view-user';

    public $user;
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['user_id'] = auth('api')->id();
        $this->user = User::with(['addresses', 'sentMessages', 'receivedMessages', 'medicalReports'])->find($data['id']);
        return $data;
    }


    // public function edit()
    // {
    //     // Redirect to the edit page of the user
    //     return Redirect::route('filament.resources.users.edit', ['record' => $this->user->id]);
    // }



    // // Load the user record based on the ID
    // public function mount(User $user)
    // {
    //     $this->user = $user;
    // }

    // // Define the content for the page
    // public function render()
    // {
    //     return view(self::$view, [
    //         'user' => $this->user
    //     ]);
    // }
}
