<?php

namespace Database\Seeders;

use App\Models\Favorites;
use App\Models\Treatment;
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
        // Fetch some users and treatments
        $users = User::all();
        $treatments = Treatment::all();

        // Ensure there are users and treatments to seed
        if ($users->isEmpty() || $treatments->isEmpty()) {
            $this->command->warn('No users or treatments found to seed Usertreatment.');
            return;
        }

        // Seed user treatments
        foreach ($users as $user) {
            // Assign a random number of treatments to each user
            $randomtreatments = $treatments->random(rand(1, $treatments->count()));

            foreach ($randomtreatments as $treatment) {
                Favorites::create([
                    'user_id' => $user->id,
                    'treatment_id' => $treatment->id,
                ]);
            }
        }

        $this->command->info('User treatments seeded successfully.');
    }
}
