<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificamos si el usuario está logueado con nuestro sistema
        if (!session()->has('idPersona')) {
            // Guardamos la URL donde estaba para llevarlo de vuelta después
            session()->put('url.intended', $request->fullUrl());
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página');
        }

        // Si tiene recordarme activado, mantenemos la sesión viva
        if (session()->has('remember_me')) {
            // Regeneramos el ID de sesión para que no expire
            session()->regenerate();
        }

        return $next($request);
    }
}
