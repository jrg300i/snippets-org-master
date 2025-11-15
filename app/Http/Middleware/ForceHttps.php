<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si no es HTTPS y estamos en producción
        if (!$request->secure() && app()->environment('production')) {
            // Redirigir a la versión HTTPS de la URL
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}