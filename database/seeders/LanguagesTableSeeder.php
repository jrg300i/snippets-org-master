<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    public function run()
    {
        // Primero, asegurarnos de que la tabla tenga los datos básicos
        $languages = [
            ['name' => 'PHP', 'color' => '#777BB4'],
            ['name' => 'JavaScript', 'color' => '#f7df1e'],
            ['name' => 'Python', 'color' => '#3776ab'],
            ['name' => 'Java', 'color' => '#ed8b00'],
            ['name' => 'HTML', 'color' => '#e34f26'],
            ['name' => 'CSS', 'color' => '#1572b6'],
            ['name' => 'Bash', 'color' => '#4eaa25'],
            ['name' => 'Shell', 'color' => '#4eaa25'],
            ['name' => 'SQL', 'color' => '#e38c00'],
            ['name' => 'Ruby', 'color' => '#cc342d'],
        ];

        foreach ($languages as $lang) {
            // Usar updateOrInsert para evitar duplicados
            DB::table('languages')->updateOrInsert(
                ['name' => $lang['name']],
                [
                    'color' => $lang['color'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            $this->command->info("✅ Procesado: {$lang['name']} - {$lang['color']}");
        }

        $this->command->info('🎨 Todos los lenguajes han sido procesados!');
    }
}