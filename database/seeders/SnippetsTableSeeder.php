<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Language;
use App\Models\User;

class SnippetsTableSeeder extends Seeder
{
    public function run()
    {
        $developer = User::where('email', 'developer@example.com')->first();
        $tester = User::where('email', 'tester@example.com')->first();

        if (!$developer || !$tester) {
            $this->command->warn('⚠️  Faltan usuarios demo, se omite el seeder de snippets.');
            return;
        }

        $cat = fn (string $name) => Category::where('name', $name)->value('id');
        $lang = fn (string $name) => Language::where('name', $name)->value('id');

        $snippets = [
            [
                'user_id' => $developer->id,
                'title' => 'Eliminar archivos antiguos (más de 30 días)',
                'description' => 'Elimina archivos de un directorio que no se modifican desde hace 30 días.',
                'code' => "find /var/log/app -type f -mtime +30 -exec rm -f {} \\;\necho 'Archivos antiguos eliminados.'",
                'tags' => ['find', 'limpieza', 'cron'],
                'category_id' => $cat('Gestión de Archivos'),
                'language_id' => $lang('Bash'),
            ],
            [
                'user_id' => $developer->id,
                'title' => 'Backup automático de PostgreSQL',
                'description' => 'Volcado de una base PostgreSQL comprimido con fecha.',
                'code' => "#!/bin/bash\nDUMP_DIR=\"/backups/postgres\"\nmkdir -p \"\$DUMP_DIR\"\npg_dump -U app -d snipets_db | gzip > \"\$DUMP_DIR/backup_\$(date +%Y%m%d_%H%M).sql.gz\"\necho 'Backup completado.'",
                'tags' => ['backup', 'postgres', 'pg_dump'],
                'category_id' => $cat('Copia de Seguridad y Recuperación'),
                'language_id' => $lang('Bash'),
            ],
            [
                'user_id' => $developer->id,
                'title' => 'Reiniciar servicio Apache',
                'description' => 'Reinicia de forma segura el servicio Apache y verifica su estado.',
                'code' => "sudo systemctl restart apache2\nsudo systemctl status apache2 --no-pager",
                'tags' => ['systemctl', 'apache', 'servicios'],
                'category_id' => $cat('Administración del Sistema'),
                'language_id' => $lang('Shell'),
            ],
            [
                'user_id' => $developer->id,
                'title' => 'Monitorear uso de memoria y procesos',
                'description' => 'Reporte rápido de memoria y los 5 procesos que más consumen.',
                'code' => "free -h\nps aux --sort=-%mem | head -6",
                'tags' => ['monitoreo', 'memoria', 'ps'],
                'category_id' => $cat('Monitoreo'),
                'language_id' => $lang('Bash'),
            ],
            [
                'user_id' => $tester->id,
                'title' => 'Ordenamiento rápido (quicksort) en JavaScript',
                'description' => 'Implementación recursiva del algoritmo quicksort.',
                'code' => "function quickSort(arr) {\n  if (arr.length <= 1) return arr;\n  const pivote = arr[0];\n  const izq = [], der = [];\n  for (let i = 1; i < arr.length; i++) {\n    arr[i] < pivote ? izq.push(arr[i]) : der.push(arr[i]);\n  }\n  return [...quickSort(izq), pivote, ...quickSort(der)];\n}\n\nconsole.log(quickSort([9, 3, 7, 1, 8]));",
                'tags' => ['algoritmo', 'ordenamiento', 'recursion'],
                'category_id' => $cat('Pruebas'),
                'language_id' => $lang('JavaScript'),
            ],
            [
                'user_id' => $tester->id,
                'title' => 'Validar JSON desde la terminal',
                'description' => 'Comprueba sintaxis JSON y lo muestra formateado.',
                'code' => "echo '{\"id\": 1, \"nombre\": \"demo\"}' | python3 -m json.tool",
                'tags' => ['json', 'validación', 'python'],
                'category_id' => $cat('Utilidades'),
                'language_id' => $lang('Python'),
            ],
        ];

        foreach ($snippets as $snippet) {
            DB::table('snippets')->updateOrInsert(
                ['title' => $snippet['title'], 'user_id' => $snippet['user_id']],
                [
                    'description' => $snippet['description'],
                    'code' => $snippet['code'],
                    'tags' => json_encode($snippet['tags']),
                    'category_id' => $snippet['category_id'],
                    'language_id' => $snippet['language_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        $this->command->info('✅ Snippets demo creados: ' . count($snippets));
    }
}