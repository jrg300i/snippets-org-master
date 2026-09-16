<?php

namespace App\Services;

use App\Models\Snippet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Integración con thiscodeworks.com (reglas.md §17).
 *
 * La API pública solo soporta CREAR snippets:
 *   POST https://api.thiscodeworks.com/api/snippets
 *   Headers: Authorization: Bearer <api_key>
 *   Body JSON: { title, code, tags[] }
 *   Respuesta: 201 "Snippet saved"
 *
 * No existen endpoints de actualización, borrado ni lectura individual.
 * El servicio degrada de forma segura: si está deshabilitado o la API falla,
 * la operación local nunca se ve afectada (los errores solo se registran).
 */
class ThisCodeWorksService
{
    public function enabled(): bool
    {
        return config('services.thiscodeworks.enabled', false)
            && (bool) $this->apiKey();
    }

    /**
     * Clave de API efectiva: la del usuario autenticado si la ha definido
     * (perfil), o la global del .env en caso contrario.
     */
    public function apiKey(): ?string
    {
        $user = auth()->user();

        if ($user && filled($user->thiscodeworks_api_key)) {
            return $user->thiscodeworks_api_key;
        }

        return config('services.thiscodeworks.api_key');
    }

    public function apiUrl(): string
    {
        return rtrim((string) config('services.thiscodeworks.api_url', 'https://api.thiscodeworks.com'), '/');
    }

    public function baseUrl(): string
    {
        return rtrim((string) config('services.thiscodeworks.base_url', 'https://www.thiscodeworks.com'), '/');
    }

    /**
     * Comprobación ligera de que thiscodeworks.com responde.
     * Se cachea 60 segundos para no golpear su API en cada petición.
     */
    public function isOnline(): bool
    {
        return (bool) Cache::remember('thiscodeworks_online', 60, function () {
            try {
                $response = Http::timeout(5)
                    ->acceptJson()
                    ->get($this->baseUrl() . '/api/snippets');

                return $response->successful();
            } catch (\Throwable $e) {
                Log::warning('thiscodeworks: no responde (health check): ' . $e->getMessage());
                return false;
            }
        });
    }

    /**
     * Buscar en el listado público (newest-first) el id remoto de un snippet
     * recién publicado, para poder guardar el enlace permanente.
     */
    public function findRemoteId(string $title, string $code): ?string
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get($this->apiUrl() . '/api/snippets');

            if (!$response->successful()) {
                return null;
            }

            $normalizeCode = fn (string $c) => trim(preg_replace('/\s+/', ' ', $c));
            $candidate = $normalizeCode($code);

            foreach ($response->json() ?: [] as $snippet) {
                $remoteCode = $normalizeCode($snippet['code'] ?? '');
                if (($snippet['title'] ?? '') === $title && $remoteCode === $candidate) {
                    return $snippet['_id'] ?? $snippet['id'] ?? null;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('thiscodeworks: no se pudo obtener el id remoto: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * URL permanente al snippet publicado.
     * Verifica primero si el id remoto ya se conoce; si no, intenta descubrirlo.
     */
    public function remoteUrl(Snippet $snippet): ?string
    {
        $remoteId = $snippet->thiscodeworks_id;

        if (!$remoteId && $snippet->exists) {
            $remoteId = $this->findRemoteId($snippet->title, $snippet->code);
            if ($remoteId) {
                $this->storeRemote($snippet, $remoteId);
            }
        }

        if (!$remoteId) {
            return null;
        }

        return $this->publicUrl($snippet, $remoteId);
    }

    /**
     * Publicar un snippet local en thiscodeworks.com.
     *
     * @return bool true si el guardado remoto tuvo éxito (201).
     */
    public function publish(Snippet $snippet): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        if ($snippet->thiscodeworks_id) {
            // Ya publicado: la API no soporta actualización, no repetimos.
            return true;
        }

        try {
            $response = Http::timeout(12)
                ->withToken($this->apiKey())
                ->acceptJson()
                ->post($this->apiUrl() . '/api/snippets', $this->payload($snippet));

            if (!$response->successful()) {
                Log::warning('thiscodeworks: POST falló con estado ' . $response->status() . ': ' . substr($response->body(), 0, 200));
                return false;
            }

            // El id no viene en la respuesta; se descubre del listado público.
            $remoteId = $this->findRemoteId($snippet->title, $snippet->code);
            if ($remoteId) {
                $this->storeRemote($snippet, $remoteId);
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('thiscodeworks: error de conexión al publicar: ' . $e->getMessage());
            return false;
        }
    }

    private function storeRemote(Snippet $snippet, ?string $remoteId): void
    {
        $snippet->thiscodeworks_id = $remoteId;
        $snippet->thiscodeworks_url = $remoteId ? $this->publicUrl($snippet, $remoteId) : null;
        $snippet->saveQuietly();
    }

    private function publicUrl(Snippet $snippet, string $remoteId): string
    {
        $slug = Str::slug($snippet->title);

        return $this->baseUrl() . '/' . ($slug ? $slug . '/' : '') . $remoteId;
    }

    private function payload(Snippet $snippet): array
    {
        $tags = $snippet->tags;

        return [
            'title' => $snippet->title,
            'code' => $snippet->code,
            'tags' => is_array($tags) ? array_values($tags) : [],
        ];
    }
}