<?php

namespace Database\Seeders;

use App\Models\categories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = categories::create([
            'name' => 'root',
            'slug' => 'root',
            'visibility' => false,
        ]);
        $category->image()->create(['path' => 'https://pic.onlinewebfonts.com/thumbnails/icons_471407.svg']);

        $category = categories::create([
            'name' => 'Slimming',
            'slug' => 'slimming',
            'visibility' => false,
        ]);
        $category->image()->create(['path' => 'https://cdn.vectorstock.com/i/1000x1000/77/81/fat-people-icon-vector-22007781.webp']);

        $category = categories::create([
            'name' => 'Fat',
            'slug' => 'fat ',
            'visibility' => false,
        ]);
        $category->image()->create(['path' => 'public/storage/categories/1.png']);
    }
}
