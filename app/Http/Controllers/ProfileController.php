<?php

namespace App\Http\Controllers;

use App\Models\Snippet;
use App\Models\Category;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        
        // Cargar estadísticas del usuario
        $stats = [
            'total_snippets' => Snippet::where('user_id', $user->id)->count(),
            'total_categories' => Snippet::where('user_id', $user->id)
                ->distinct('category_id')
                ->count('category_id'),
            'total_languages' => Snippet::where('user_id', $user->id)
                ->distinct('language_id')
                ->count('language_id'),
            'member_since' => $this->formatMemberSince($user->created_at),
        ];

        return view('profile.edit', compact('user', 'stats'));
    }

    private function formatMemberSince($createdAt)
    {
        $now = now();
        $diff = $createdAt->diff($now);
        
        if ($diff->y > 0) {
            return $diff->y . ' ' . ($diff->y == 1 ? 'año' : 'años');
        } elseif ($diff->m > 0) {
            return $diff->m . ' ' . ($diff->m == 1 ? 'mes' : 'meses');
        } elseif ($diff->d > 0) {
            if ($diff->d == 1) {
                return '1 día';
            } elseif ($diff->d < 7) {
                return $diff->d . ' días';
            } else {
                $weeks = floor($diff->d / 7);
                return $weeks . ' ' . ($weeks == 1 ? 'semana' : 'semanas');
            }
        } elseif ($diff->h > 0) {
            return $diff->h . ' ' . ($diff->h == 1 ? 'hora' : 'horas');
        } else {
            return 'Menos de 1 hora';
        }
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        return redirect()->route('profile.edit')
            ->with('success', 'Perfil actualizado exitosamente.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Contraseña actualizada exitosamente.');
    }

    /**
     * Generar respaldo de datos en formato Seeder
     */
    public function backup(Request $request)
    {
        try {
            $user = Auth::user();
            $backupType = $request->get('backup_type', 'snippets');
            $includeCode = $request->boolean('include_code', true);

            // Validar que el usuario tenga datos para respaldar
            $snippetCount = Snippet::where('user_id', $user->id)->count();
            
            if ($snippetCount === 0) {
                return redirect()->route('profile.edit')
                    ->with('error', 'No tienes snippets para respaldar.');
            }

            // Obtener datos del usuario
            $userSnippets = Snippet::where('user_id', $user->id)
                ->with(['category', 'language'])
                ->get();

            // Obtener TODAS las categorías de la base de datos (no solo las del usuario)
            $allCategories = Category::all();

            // Generar contenido para los seeders
            $seederContent = $this->generateSeederContent($user, $userSnippets, $allCategories, $backupType, $includeCode);

            // Crear archivo ZIP con los seeders
            $zipResult = $this->createSeederZip($seederContent, $user->name);
            
            if (!$zipResult['success']) {
                throw new \Exception($zipResult['error']);
            }

            $zipFileName = $zipResult['filename'];

            // Preparar headers para la descarga
            $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name);
            $downloadName = "snippets_backup_{$cleanName}_" . now()->format('Y-m-d_His') . ".zip";

            // Devolver el ZIP como descarga
            return Response::download($zipFileName, $downloadName)
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Error generating backup for user ' . Auth::id() . ': ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return redirect()->route('profile.edit')
                ->with('error', 'Error al generar el respaldo: ' . $e->getMessage());
        }
    }

    /**
     * Generar contenido para los archivos seeder
     */
    private function generateSeederContent($user, $snippets, $allCategories, $backupType, $includeCode)
    {
        $content = [];
        $timestamp = now()->format('Y_m_d_His');

        // 1. Seeder de Categorías (TODAS las categorías de la base de datos)
        $categoriesContent = $this->generateCategoriesSeeder($allCategories, $timestamp);
        $content["CategoriesSeeder_{$timestamp}.php"] = $categoriesContent;

        // 2. Seeder de Lenguajes (todos los lenguajes de la base de datos)
        $allLanguages = Language::all();
        $languagesContent = $this->generateLanguagesSeeder($allLanguages, $timestamp);
        $content["LanguagesSeeder_{$timestamp}.php"] = $languagesContent;

        // 3. Seeder de Snippets (solo si hay snippets)
        if ($snippets->count() > 0) {
            $snippetsContent = $this->generateSnippetsSeeder($snippets, $timestamp, $includeCode);
            $content["SnippetsSeeder_{$timestamp}.php"] = $snippetsContent;
        }

        // 4. Seeder de Usuarios (opcional para respaldo completo)
        if ($backupType === 'full') {
            $usersContent = $this->generateUsersSeeder($user, $timestamp);
            $content["UsersSeeder_{$timestamp}.php"] = $usersContent;
        }

        // 5. Archivo README con instrucciones
        $readmeContent = $this->generateReadme($user, $snippets, $backupType, $timestamp);
        $content["README.md"] = $readmeContent;

        return $content;
    }

    /**
     * Generar seeder de categorías con TODAS las categorías de la BD
     */
    private function generateCategoriesSeeder($categories, $timestamp)
    {
        $categoriesArray = $categories->map(function($category) {
            return [
                'name' => $category->name,
                'description' => $category->description ?? '',
                'proposito' => $category->proposito ?? '',
            ];
        })->toArray();

        $categoriesExport = var_export($categoriesArray, true);

        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Console\\Seeds\\WithoutModelEvents;\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use App\\Models\\Category;\n";
        $content .= "use Carbon\\Carbon;\n\n";
        $content .= "class CategoriesSeeder_{$timestamp} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        $content .= "        \$categories = {$categoriesExport};\n\n";
        $content .= "        \$timestamp = Carbon::now();\n\n";
        $content .= "        foreach (\$categories as \$category) {\n";
        $content .= "            Category::firstOrCreate(\n";
        $content .= "                ['name' => \$category['name']],\n";
        $content .= "                [\n";
        $content .= "                    'description' => \$category['description'],\n";
        $content .= "                    'proposito' => \$category['proposito'],\n";
        $content .= "                    'created_at' => \$timestamp,\n";
        $content .= "                    'updated_at' => \$timestamp\n";
        $content .= "                ]\n";
        $content .= "            );\n";
        $content .= "        }\n\n";
        $content .= "        \$this->command->info('✅ Tabla de categorías poblada exitosamente desde el respaldo!');\n";
        $content .= "        \$this->command->info('📊 Total de categorías: ' . count(\$categories));\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    /**
     * Generar seeder de lenguajes con TODOS los lenguajes de la BD
     */
    private function generateLanguagesSeeder($languages, $timestamp)
    {
        $languagesArray = $languages->map(function($language) {
            return [
                'name' => $language->name,
                'color' => $language->color,
                'slug' => $language->slug ?? strtolower(str_replace(' ', '-', $language->name)),
                'description' => $language->description ?? '',
                'is_active' => $language->is_active ?? true,
            ];
        })->toArray();

        $languagesExport = var_export($languagesArray, true);

        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Console\\Seeds\\WithoutModelEvents;\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use App\\Models\\Language;\n";
        $content .= "use Carbon\\Carbon;\n\n";
        $content .= "class LanguagesSeeder_{$timestamp} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        $content .= "        \$languages = {$languagesExport};\n\n";
        $content .= "        \$timestamp = Carbon::now();\n\n";
        $content .= "        foreach (\$languages as \$lang) {\n";
        $content .= "            Language::firstOrCreate(\n";
        $content .= "                ['name' => \$lang['name']],\n";
        $content .= "                [\n";
        $content .= "                    'color' => \$lang['color'],\n";
        $content .= "                    'slug' => \$lang['slug'],\n";
        $content .= "                    'description' => \$lang['description'],\n";
        $content .= "                    'is_active' => \$lang['is_active'],\n";
        $content .= "                    'created_at' => \$timestamp,\n";
        $content .= "                    'updated_at' => \$timestamp\n";
        $content .= "                ]\n";
        $content .= "            );\n";
        $content .= "        }\n\n";
        $content .= "        \$this->command->info('🎨 Todos los lenguajes han sido procesados desde el backup!');\n";
        $content .= "        \$this->command->info('📊 Total de lenguajes: ' . count(\$languages));\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    /**
     * Generar seeder de snippets
     */
    private function generateSnippetsSeeder($snippets, $timestamp, $includeCode)
    {
        $snippetsArray = $snippets->map(function($snippet) use ($includeCode) {
            $data = [
                'title' => $snippet->title,
                'description' => $snippet->description ?? '',
                'category_id' => $snippet->category_id,
                'language_id' => $snippet->language_id,
                'user_id' => $snippet->user_id,
                'is_public' => $snippet->is_public ?? false,
                'favorite' => $snippet->favorite ?? false,
                'tags' => $snippet->tags ?? '',
                'created_at' => $snippet->created_at->toDateTimeString(),
                'updated_at' => $snippet->updated_at->toDateTimeString(),
            ];

            if ($includeCode) {
                $data['code'] = $snippet->code;
            }

            return $data;
        })->toArray();

        $snippetsExport = var_export($snippetsArray, true);
        $codeIncluded = $includeCode ? 'true' : 'false';

        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Console\\Seeds\\WithoutModelEvents;\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use App\\Models\\Snippet;\n\n";
        $content .= "class SnippetsSeeder_{$timestamp} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        $content .= "        \$snippets = {$snippetsExport};\n\n";
        $content .= "        \$total = count(\$snippets);\n";
        $content .= "        \$withCode = {$codeIncluded};\n\n";
        $content .= "        foreach (\$snippets as \$snippet) {\n";
        $content .= "            Snippet::firstOrCreate(\n";
        $content .= "                [\n";
        $content .= "                    'title' => \$snippet['title'],\n";
        $content .= "                    'user_id' => \$snippet['user_id']\n";
        $content .= "                ],\n";
        $content .= "                \$snippet\n";
        $content .= "            );\n";
        $content .= "        }\n\n";
        $content .= "        \$this->command->info('✅ Snippets table seeded successfully from backup!');\n";
        $content .= "        \$this->command->info('📊 Total snippets: ' . \$total);\n";
        $content .= "        \$this->command->info('💻 Code included: ' . (\$withCode ? 'Yes' : 'No'));\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    /**
     * Generar seeder de usuarios
     */
    private function generateUsersSeeder($user, $timestamp)
    {
        $usersArray = [
            [
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'password123', // Password por defecto
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
            ]
        ];

        $usersExport = var_export($usersArray, true);

        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Console\\Seeds\\WithoutModelEvents;\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use App\\Models\\User;\n";
        $content .= "use Illuminate\\Support\\Facades\\Hash;\n";
        $content .= "use Carbon\\Carbon;\n\n";
        $content .= "class UsersSeeder_{$timestamp} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        $content .= "        \$users = {$usersExport};\n\n";
        $content .= "        \$timestamp = Carbon::now();\n\n";
        $content .= "        foreach (\$users as \$user) {\n";
        $content .= "            User::firstOrCreate(\n";
        $content .= "                ['email' => \$user['email']],\n";
        $content .= "                [\n";
        $content .= "                    'name' => \$user['name'],\n";
        $content .= "                    'password' => Hash::make(\$user['password']),\n";
        $content .= "                    'email_verified_at' => \$user['email_verified_at'] ? \$timestamp : null,\n";
        $content .= "                    'created_at' => \$timestamp,\n";
        $content .= "                    'updated_at' => \$timestamp\n";
        $content .= "                ]\n";
        $content .= "            );\n";
        $content .= "        }\n\n";
        $content .= "        \$this->command->info('✅ Users table seeded successfully from backup!');\n";
        $content .= "        \$this->command->info('👤 Usuarios creados:');\n";
        $content .= "        foreach (\$users as \$user) {\n";
        $content .= "            \$this->command->info('   - ' . \$user['email'] . ' / ' . \$user['password']);\n";
        $content .= "        }\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    /**
     * Generar archivo README
     */
    private function generateReadme($user, $snippets, $backupType, $timestamp)
    {
        $snippetCount = $snippets->count();
        $categoryCount = Category::count();
        $languageCount = Language::count();

        $readme = "# Respaldo de Snippets - {$user->name}\n\n";
        $readme .= "## Información del Respaldo\n";
        $readme .= "- **Fecha de generación:** " . now()->format('Y-m-d H:i:s') . "\n";
        $readme .= "- **Tipo de respaldo:** {$backupType}\n";
        $readme .= "- **Usuario:** {$user->name} ({$user->email})\n";
        $readme .= "- **Total de snippets:** {$snippetCount}\n";
        $readme .= "- **Total de categorías disponibles:** {$categoryCount}\n";
        $readme .= "- **Total de lenguajes disponibles:** {$languageCount}\n\n";
        $readme .= "## Archivos Incluidos\n\n";
        $readme .= "1. **CategoriesSeeder_{$timestamp}.php** - TODAS las categorías del sistema\n";
        $readme .= "2. **LanguagesSeeder_{$timestamp}.php** - TODOS los lenguajes del sistema\n";
        $readme .= "3. **SnippetsSeeder_{$timestamp}.php** - Snippets del usuario\n";
        
        if ($backupType === 'full') {
            $readme .= "4. **UsersSeeder_{$timestamp}.php** - Información del usuario\n\n";
        } else {
            $readme .= "\n";
        }
        
        $readme .= "## Instrucciones de Uso\n\n";
        $readme .= "1. Copia los archivos a la carpeta `database/seeders/`\n";
        $readme .= "2. Ejecuta los seeders en orden:\n\n";
        $readme .= "```bash\n";
        $readme .= "php artisan db:seed --class=CategoriesSeeder_{$timestamp}\n";
        $readme .= "php artisan db:seed --class=LanguagesSeeder_{$timestamp}\n";
        $readme .= "php artisan db:seed --class=SnippetsSeeder_{$timestamp}\n";
        
        if ($backupType === 'full') {
            $readme .= "php artisan db:seed --class=UsersSeeder_{$timestamp}\n";
        }
        
        $readme .= "```\n\n";
        $readme .= "## Notas\n";
        $readme .= "- Los seeders usan `firstOrCreate()` para evitar duplicados\n";
        $readme .= "- Se incluyen TODAS las categorías y lenguajes del sistema\n";
        $readme .= "- Las contraseñas de usuario se establecen como 'password123'\n";
        $readme .= "- Los timestamps originales se preservan en los snippets\n";

        return $readme;
    }

    /**
     * Crear archivo ZIP con los seeders
     */
    private function createSeederZip($seederContent, $userName)
    {
        $timestamp = now()->format('Y_m_d_His');
        $zipFileName = "snippets_backup_{$userName}_{$timestamp}.zip";
        $tempPath = storage_path('app/temp/' . $zipFileName);

        try {
            // Crear directorio temporal si no existe
            File::ensureDirectoryExists(dirname($tempPath));

            // Crear archivo ZIP
            $zip = new ZipArchive();
            $zipStatus = $zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            
            if ($zipStatus !== TRUE) {
                throw new \Exception("No se pudo crear el archivo ZIP. Código de error: " . $zipStatus);
            }

            foreach ($seederContent as $filename => $content) {
                if (!$zip->addFromString($filename, $content)) {
                    throw new \Exception("No se pudo agregar el archivo: " . $filename);
                }
            }

            if (!$zip->close()) {
                throw new \Exception("No se pudo cerrar el archivo ZIP");
            }

            // Verificar que el archivo se creó correctamente
            if (!file_exists($tempPath)) {
                throw new \Exception("El archivo ZIP no se generó correctamente");
            }

            if (filesize($tempPath) === 0) {
                throw new \Exception("El archivo ZIP está vacío");
            }

            return [
                'success' => true,
                'filename' => $tempPath
            ];

        } catch (\Exception $e) {
            // Limpiar archivo temporal si existe
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}