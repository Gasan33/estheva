<?php

namespace Database\Seeders;

use App\Models\Favorites;
use App\Models\Services;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoritesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch some users and services
        $users = User::all();
        $services = Services::all();

        // Ensure there are users and services to seed
        if ($users->isEmpty() || $services->isEmpty()) {
            $this->command->warn('No users or services found to seed UserService.');
            return;
        }

        // Seed user services
        foreach ($users as $user) {
            // Assign a random number of services to each user
            $randomServices = $services->random(rand(1, $services->count()));

            foreach ($randomServices as $service) {
                Favorites::create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                ]);
            }
        }

        $this->command->info('User services seeded successfully.');
    }
}
