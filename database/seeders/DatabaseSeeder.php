<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserTableSeeder::class,
            CategoriesTableSeeder::class,
            LanguagesTableSeeder::class,    // ← Agregar este seeder
            // otros seeders...
        ]);
    }
}