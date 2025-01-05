<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            DoctorsSeeder::class,
            CategoriesSeeder::class,
            AddressesSeeder::class,
            AddressesSeeder::class,
            ServicesSeeder::class,
            ReviewSeeder::class,
            FavoritesSeeder::class,
            AdvertisementsSeeder::class,
            AppointmentSeeder::class,
            MessagesSeeder::class,
            MedicalReportsSeeder::class,
            DoctorServiceSeeder::class,
        ]);
    }
}
