<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Language;
use App\Models\Snippet;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * Generación de respaldos descargables en formato Seeder (reglas.md §17).
 */
class BackupService
{
    /**
     * Crear un ZIP con los seeders (categorías, lenguajes, snippets y opcionalmente el usuario).
     *
     * @return array{success: bool, filename?: string, error?: string}
     */
    public function createBackup(User $user, string $backupType = 'snippets', bool $includeCode = true): array
    {
        $snippetCount = Snippet::where('user_id', $user->id)->count();

        if ($snippetCount === 0) {
            return ['success' => false, 'error' => 'No tienes snippets para respaldar.'];
        }

        try {
            $snippets = Snippet::where('user_id', $user->id)
                ->with(['category', 'language'])
                ->get();

            $seederContent = $this->generateContent($user, $snippets, $backupType, $includeCode);

            return $this->createZip($seederContent, $user->name);
        } catch (\Throwable $e) {
            Log::error('Error generating backup for user ' . $user->id . ': ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error al generar el respaldo: ' . $e->getMessage()];
        }
    }

    private function generateContent(User $user, $snippets, string $backupType, bool $includeCode): array
    {
        $content = [];
        $timestamp = now()->format('Y_m_d_His');

        $content["CategoriesSeeder_{$timestamp}.php"] = $this->categoriesSeeder(Category::all(), $timestamp);
        $content["LanguagesSeeder_{$timestamp}.php"] = $this->languagesSeeder(Language::all(), $timestamp);

        if ($snippets->count() > 0) {
            $content["SnippetsSeeder_{$timestamp}.php"] = $this->snippetsSeeder($snippets, $timestamp, $includeCode);
        }

        if ($backupType === 'full') {
            $content["UsersSeeder_{$timestamp}.php"] = $this->usersSeeder($user, $timestamp);
        }

        $content['README.md'] = $this->readme($user, $snippets, $backupType, $timestamp, $includeCode);

        return $content;
    }

    private function categoriesSeeder($categories, string $timestamp): string
    {
        $categoriesArray = $categories->map(fn ($category) => [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description ?? '',
            'proposito' => $category->proposito ?? '',
            'thiscodeworks_id' => $category->thiscodeworks_id,
            'thiscodeworks_url' => $category->thiscodeworks_url,
            'created_at' => $category->created_at->toDateTimeString(),
            'updated_at' => $category->updated_at->toDateTimeString(),
        ])->toArray();

        $categoriesExport = var_export($categoriesArray, true);

        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use Illuminate\\Support\\Facades\\DB;\n\n";
        $content .= "class CategoriesSeeder_{$timestamp} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        $content .= "        \$categories = {$categoriesExport};\n\n";
        $content .= "        foreach (\$categories as \$category) {\n";
        $content .= "            DB::table('categories')->updateOrInsert(\n";
        $content .= "                ['id' => \$category['id']],\n";
        $content .= "                \$category\n";
        $content .= "            );\n";
        $content .= "        }\n\n";
        $content .= "        \$this->command->info('Categorías procesadas desde el respaldo: ' . count(\$categories));\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    private function languagesSeeder($languages, string $timestamp): string
    {
        $languagesArray = $languages->map(fn ($language) => [
            'id' => $language->id,
            'name' => $language->name,
            'color' => $language->color,
            'slug' => $language->slug ?? strtolower(str_replace(' ', '-', $language->name)),
            'description' => $language->description ?? '',
            'is_active' => $language->is_active ?? true,
            'created_at' => $language->created_at->toDateTimeString(),
            'updated_at' => $language->updated_at->toDateTimeString(),
        ])->toArray();

        $languagesExport = var_export($languagesArray, true);

        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use Illuminate\\Support\\Facades\\DB;\n\n";
        $content .= "class LanguagesSeeder_{$timestamp} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        $content .= "        \$languages = {$languagesExport};\n\n";
        $content .= "        foreach (\$languages as \$lang) {\n";
        $content .= "            DB::table('languages')->updateOrInsert(\n";
        $content .= "                ['id' => \$lang['id']],\n";
        $content .= "                \$lang\n";
        $content .= "            );\n";
        $content .= "        }\n\n";
        $content .= "        \$this->command->info('Lenguajes procesados desde el respaldo: ' . count(\$languages));\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    private function snippetsSeeder($snippets, string $timestamp, bool $includeCode): string
    {
        $snippetsArray = $snippets->map(function ($snippet) use ($includeCode) {
            return [
                'id' => $snippet->id,
                'title' => $snippet->title,
                'description' => $snippet->description ?? '',
                'code' => $includeCode ? $snippet->code : '',
                'category_id' => $snippet->category_id,
                'language_id' => $snippet->language_id,
                'user_id' => $snippet->user_id,
                'tags' => json_encode(array_values((array) $snippet->tags), JSON_UNESCAPED_UNICODE),
                'thiscodeworks_id' => $snippet->thiscodeworks_id,
                'thiscodeworks_url' => $snippet->thiscodeworks_url,
                'created_at' => $snippet->created_at->toDateTimeString(),
                'updated_at' => $snippet->updated_at->toDateTimeString(),
            ];
        })->toArray();

        $snippetsExport = var_export($snippetsArray, true);
        $codeIncluded = $includeCode ? 'true' : 'false';

        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use Illuminate\\Support\\Facades\\DB;\n\n";
        $content .= "class SnippetsSeeder_{$timestamp} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        $content .= "        \$snippets = {$snippetsExport};\n\n";
        $content .= "        \$total = count(\$snippets);\n";
        $content .= "        \$withCode = {$codeIncluded};\n\n";
        $content .= "        foreach (\$snippets as \$snippet) {\n";
        $content .= "            DB::table('snippets')->updateOrInsert(\n";
        $content .= "                ['id' => \$snippet['id']],\n";
        $content .= "                \$snippet\n";
        $content .= "            );\n";
        $content .= "        }\n\n";
        $content .= "        \$this->command->info('Snippets procesados desde el respaldo: ' . \$total);\n";
        $content .= "        \$this->command->info('Code included: ' . (\$withCode ? 'Yes' : 'No'));\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    private function usersSeeder(User $user, string $timestamp): string
    {
        $usersExport = var_export([
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'password123',
                'role' => $user->role ?? 'user',
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
                'created_at' => $user->created_at->toDateTimeString(),
                'updated_at' => $user->updated_at->toDateTimeString(),
            ],
        ], true);

        $content = "<?php\n\n";
        $content .= "namespace Database\\Seeders;\n\n";
        $content .= "use Illuminate\\Database\\Seeder;\n";
        $content .= "use Illuminate\\Support\\Facades\\DB;\n";
        $content .= "use Illuminate\\Support\\Facades\\Hash;\n\n";
        $content .= "class UsersSeeder_{$timestamp} extends Seeder\n";
        $content .= "{\n";
        $content .= "    public function run()\n";
        $content .= "    {\n";
        $content .= "        \$users = {$usersExport};\n\n";
        $content .= "        foreach (\$users as \$user) {\n";
        $content .= "            \$user['password'] = Hash::make(\$user['password']);\n\n";
        $content .= "            DB::table('users')->updateOrInsert(\n";
        $content .= "                ['id' => \$user['id']],\n";
        $content .= "                \$user\n";
        $content .= "            );\n";
        $content .= "        }\n\n";
        $content .= "        \$this->command->info('Usuarios procesados desde el respaldo: ' . count(\$users));\n";
        $content .= "        foreach (\$users as \$user) {\n";
        $content .= "            \$this->command->info('   - ' . \$user['email'] . ' / password123');\n";
        $content .= "        }\n";
        $content .= "    }\n";
        $content .= "}\n";

        return $content;
    }

    private function readme(User $user, $snippets, string $backupType, string $timestamp, bool $includeCode = true): string
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
        $readme .= "- Los seeders usan `DB::table()->updateOrInsert()` y conservan los IDs originales (las claves foráneas siguen siendo válidas)\n";
        $readme .= "- Se incluyen TODAS las categorías y lenguajes del sistema\n";
        $readme .= "- Las contraseñas de usuario se restablecen como 'password123'\n";
        $readme .= "- Los timestamps originales se preservan\n";
        if (!$includeCode) {
            $readme .= "- Este respaldo se generó SIN código: los snippets restaurados quedan con `code` vacío\n";
        }

        return $readme;
    }

    private function createZip(array $seederContent, string $userName): array
    {
        $timestamp = now()->format('Y_m_d_His');
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $userName);
        $zipFileName = "snippets_backup_{$safeName}_{$timestamp}.zip";
        $tempPath = storage_path('app/temp/' . $zipFileName);

        try {
            File::ensureDirectoryExists(dirname($tempPath));

            $zip = new ZipArchive();
            $zipStatus = $zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            if ($zipStatus !== true) {
                throw new \Exception('No se pudo crear el archivo ZIP. Código de error: ' . $zipStatus);
            }

            foreach ($seederContent as $filename => $content) {
                if (!$zip->addFromString($filename, $content)) {
                    throw new \Exception('No se pudo agregar el archivo: ' . $filename);
                }
            }

            if (!$zip->close()) {
                throw new \Exception('No se pudo cerrar el archivo ZIP');
            }

            if (!file_exists($tempPath) || filesize($tempPath) === 0) {
                throw new \Exception('El archivo ZIP no se generó correctamente');
            }

            return ['success' => true, 'filename' => $tempPath];
        } catch (\Exception $e) {
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}