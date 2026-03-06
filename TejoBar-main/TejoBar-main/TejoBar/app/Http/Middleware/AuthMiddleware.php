<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado usando nuestra sesión personalizada
        if (!session()->has('idPersona')) {
            // Guardar la URL actual para redirigir después del login
            session()->put('url.intended', $request->fullUrl());
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página');
        }

        // Si el usuario tiene "recordarme" activado, extender la sesión
        if (session()->has('remember_me')) {
            // Regenerar el ID de sesión para mantener la sesión activa
            session()->regenerate();
        }

        return $next($request);
    }
}
