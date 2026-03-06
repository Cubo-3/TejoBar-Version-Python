<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar si el usuario está autenticado
        if (!session()->has('idPersona')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página');
        }

        // Verificar si el usuario tiene el rol requerido
        $userRole = session('rol');
        
        if (!in_array($userRole, $roles)) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta página');
        }

        return $next($request);
    }
}
