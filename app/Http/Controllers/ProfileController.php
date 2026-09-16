<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiKeyUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Snippet;
use App\Services\AuditoriaService;
use App\Services\BackupService;
use App\Services\ThisCodeWorksService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class ProfileController extends Controller
{
    public function __construct(
        private readonly AuditoriaService $auditoria,
        private readonly BackupService $backupService,
        private readonly ThisCodeWorksService $thiscodeworks,
    ) {
    }

    public function edit()
    {
        $user = Auth::user();

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

        return view('profile.edit', [
            'user' => $user,
            'stats' => $stats,
            'thiscodeworksEnabled' => $this->thiscodeworks->enabled(),
            'effectiveApiKey' => $this->thiscodeworks->apiKey(),
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $antes = $user->toArray();

        $user->update($request->validated());

        $this->auditoria->registrar(
            'actualizar',
            'perfil',
            $user->id,
            'Datos del perfil actualizados.',
            $antes,
            $user->toArray(),
        );

        return redirect()->route('profile.edit')
            ->with('success', 'Perfil actualizado exitosamente.');
    }

    public function passwordUpdate(PasswordUpdateRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $this->auditoria->registrar(
            'actualizar',
            'perfil',
            $user->id,
            'Contraseña cambiada.',
        );

        return redirect()->route('profile.edit')
            ->with('success', 'Contraseña actualizada exitosamente.');
    }

    public function apiKeyUpdate(ApiKeyUpdateRequest $request)
    {
        $user = Auth::user();
        $key = trim((string) $request->input('thiscodeworks_api_key', ''));

        if ($key === '') {
            return redirect()->route('profile.edit')
                ->with('info', 'La API key se dejó en blanco: se mantiene la actual.');
        }

        $user->update(['thiscodeworks_api_key' => $key]);

        $this->auditoria->registrar(
            'actualizar',
            'perfil',
            $user->id,
            'API key de thiscodeworks actualizada.',
        );

        return redirect()->route('profile.edit')
            ->with('success', 'API key de thiscodeworks actualizada exitosamente.');
    }

    public function stats()
    {
        return redirect()->route('profile.edit');
    }

    public function backup(Request $request)
    {
        return $this->generateBackup($request);
    }

    public function backupStore(Request $request)
    {
        return $this->generateBackup($request);
    }

    private function generateBackup(Request $request)
    {
        $user = Auth::user();
        $backupType = $request->get('backup_type', 'snippets');
        $includeCode = $request->boolean('include_code', true);

        $result = $this->backupService->createBackup($user, $backupType, $includeCode);

        if (!$result['success']) {
            return redirect()->route('profile.edit')
                ->with('error', $result['error'] ?? 'No tienes snippets para respaldar.');
        }

        $this->auditoria->registrar(
            'backup',
            'perfil',
            $user->id,
            "Respaldo {$backupType} generado (" . ($includeCode ? 'con código' : 'sin código') . ').',
        );

        $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name);
        $downloadName = "snippets_backup_{$cleanName}_" . now()->format('Y-m-d_His') . '.zip';

        return Response::download($result['filename'], $downloadName)
            ->deleteFileAfterSend(true);
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
}