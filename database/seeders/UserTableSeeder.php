<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        // Usuario administrador
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Usuario desarrollador
        User::firstOrCreate(
            ['email' => 'developer@example.com'],
            [
                'name' => 'Desarrollador',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Usuario tester
        User::firstOrCreate(
            ['email' => 'tester@example.com'],
            [
                'name' => 'Tester',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Users table seeded successfully!');
        $this->command->info('📧 Usuarios creados:');
        $this->command->info('   - admin@example.com / password123');
        $this->command->info('   - developer@example.com / password123'); 
        $this->command->info('   - tester@example.com / password123');
    }
}