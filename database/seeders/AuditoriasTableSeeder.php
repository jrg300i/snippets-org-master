<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Auditoria;
use App\Models\User;

class AuditoriasTableSeeder extends Seeder
{
    public function run()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $developer = User::where('email', 'developer@example.com')->first();

        if (!$admin || !$developer) {
            $this->command->warn('⚠️  Faltan usuarios demo, se omite el seeder de auditorías.');
            return;
        }

        Auditoria::create([
            'user_id' => $admin->id,
            'accion' => 'login',
            'entidad' => 'usuario',
            'entidad_id' => $admin->id,
            'descripcion' => "Inicio de sesión de {$admin->email}.",
            'ip' => '127.0.0.1',
        ]);

        Auditoria::create([
            'user_id' => $developer->id,
            'accion' => 'crear',
            'entidad' => 'snippet',
            'descripcion' => 'Snippet «Backup automático de PostgreSQL» creado.',
            'datos_despues' => ['title' => 'Backup automático de PostgreSQL', 'tags' => ['backup']],
            'ip' => '127.0.0.1',
        ]);

        Auditoria::create([
            'user_id' => $admin->id,
            'accion' => 'eliminar',
            'entidad' => 'categoria',
            'descripcion' => 'Categoría «Prueba» eliminada.',
            'datos_antes' => ['name' => 'Prueba'],
            'ip' => '127.0.0.1',
        ]);

        $this->command->info('✅ Auditorías demo creadas.');
    }
}