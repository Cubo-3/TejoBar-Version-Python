<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class CustomAuthServiceProvider extends ServiceProvider
{
    /**
     * Registrar servicios.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializar servicios.
     */
    public function boot(): void
    {
        // Directiva @customauth para verificar si el usuario está logueado con nuestro sistema
        Blade::directive('customauth', function () {
            return "<?php if(session()->has('idPersona')): ?>";
        });

        // Cierra la directiva @customauth
        Blade::directive('endcustomauth', function () {
            return "<?php endif; ?>";
        });

        // Directiva @else para usar dentro de @customauth
        Blade::directive('else', function () {
            return "<?php else: ?>";
        });

        // Cierra la directiva @customhasrole
        Blade::directive('endcustomhasrole', function () {
            return "<?php endif; ?>";
        });

        // Directiva @guest para verificar si el usuario NO está logueado
        Blade::directive('customguest', function () {
            return "<?php if(!session()->has('idPersona')): ?>";
        });

        // Cierra la directiva @guest
        Blade::directive('endcustomguest', function () {
            return "<?php endif; ?>";
        });

        // Ayudante para obtener el usuario logueado
        Blade::directive('customuser', function () {
            return "<?php echo session('nombre', 'Usuario'); ?>";
        });

        // Ayudante para obtener el rol del usuario
        Blade::directive('customrole', function () {
            return "<?php echo session('rol', 'guest'); ?>";
        });

        // Ayudante para verificar si el usuario tiene un rol específico
        Blade::directive('customhasrole', function ($role) {
            return "<?php if(session('rol') === {$role}): ?>";
        });

        // Ayudante para verificar si el usuario es admin
        Blade::directive('customadmin', function () {
            return "<?php if(session('rol') === 'admin'): ?>";
        });

        // Ayudante para verificar si el usuario es capitán
        Blade::directive('customcapitan', function () {
            return "<?php if(session('rol') === 'capitan'): ?>";
        });

        // Ayudante para verificar si el usuario es jugador
        Blade::directive('customjugador', function () {
            return "<?php if(session('rol') === 'jugador'): ?>";
        });
    }
}
