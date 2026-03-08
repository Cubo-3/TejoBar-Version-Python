<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RememberMeMiddleware
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está logueado y tiene recordarme activado
        if (session()->has('idPersona') && session()->has('remember_me')) {
            // Hacemos que la sesión dure más tiempo
            $sessionLifetime = 43200; // 30 días en minutos
            config(['session.lifetime' => $sessionLifetime]);
            
            // Regeneramos el ID de sesión cada hora para mantenerla activa
            if (time() - session('last_activity', 0) > 3600) { // Cada hora
                session()->regenerate();
                session()->put('last_activity', time());
            }
        }

        return $next($request);
    }
}

