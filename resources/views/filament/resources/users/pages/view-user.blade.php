{{-- resources/views/filament/resources/user-resource/pages/view-user.blade.php --}}
<x-filament::page>
    <div class="space-y-6">
        {{-- @dd($user) --}}
        <!-- Profile Picture -->
        <div class="flex items-start justify-start gap-8">
            <div class="">
                <img src="{{ $user->profile_picture_url }}" alt="Profile Picture" class="w-32 h-32 rounded-md">
            </div>

            <!-- User Information -->
            <div class="flex flex-col flex-1 justify-between py-4">
                <div class="flex flex-col space-y-2">
                    <h1 class="font-bold text-lg">{{ $user->first_name }} {{ $user->last_name }}</h1>
                    <label class="font-normal text-md">{{ $user->email }}</label>
                    <label class="font-normal text-md">{{ $user->formatted_phone_number }}</label>
                    <label class="font-normal text-md">{{ $user->gender }}</label>
                </div>
            </div>
        </div>

        <div class="border-b border-gray-400 pt-8">

        </div>


        <!-- Add Custom Sections as Needed -->
        {{-- <div class="mt-4">
            <x-filament::button wire:click="edit" color="primary">Edit</x-filament::button>
        </div> --}}
    </div>
</x-filament::page>
