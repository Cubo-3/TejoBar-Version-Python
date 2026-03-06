<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RememberMeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado y tiene "recordarme" activado
        if (session()->has('idPersona') && session()->has('remember_me')) {
            // Extender la duración de la sesión
            $sessionLifetime = 43200; // 30 días en minutos
            config(['session.lifetime' => $sessionLifetime]);
            
            // Regenerar el ID de sesión periódicamente para mantener la sesión activa
            if (time() - session('last_activity', 0) > 3600) { // Cada hora
                session()->regenerate();
                session()->put('last_activity', time());
            }
        }

        return $next($request);
    }
}

