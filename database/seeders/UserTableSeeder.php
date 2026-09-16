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
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'developer@example.com'],
            [
                'name' => 'Desarrollador',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'user',
            ]
        );

        User::updateOrCreate(
            ['email' => 'tester@example.com'],
            [
                'name' => 'Tester',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'user',
            ]
        );

        $this->command->info('✅ Users table seeded successfully!');
        $this->command->info('📧 Usuarios creados:');
        $this->command->info('   - admin@example.com / password123 (admin)');
        $this->command->info('   - developer@example.com / password123');
        $this->command->info('   - tester@example.com / password123');
    }
}