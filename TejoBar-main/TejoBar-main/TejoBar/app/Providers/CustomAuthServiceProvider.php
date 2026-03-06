<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class CustomAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Directiva @customauth para verificar si el usuario está autenticado con nuestro sistema personalizado
        Blade::directive('customauth', function () {
            return "<?php if(session()->has('idPersona')): ?>";
        });

        // Directiva @endcustomauth
        Blade::directive('endcustomauth', function () {
            return "<?php endif; ?>";
        });

        // Directiva @else para usar dentro de @customauth
        Blade::directive('else', function () {
            return "<?php else: ?>";
        });

        // Directiva @endcustomhasrole
        Blade::directive('endcustomhasrole', function () {
            return "<?php endif; ?>";
        });

        // Directiva @guest para verificar si el usuario NO está autenticado
        Blade::directive('customguest', function () {
            return "<?php if(!session()->has('idPersona')): ?>";
        });

        // Directiva @endguest
        Blade::directive('endcustomguest', function () {
            return "<?php endif; ?>";
        });

        // Helper para obtener el usuario autenticado
        Blade::directive('customuser', function () {
            return "<?php echo session('nombre', 'Usuario'); ?>";
        });

        // Helper para obtener el rol del usuario
        Blade::directive('customrole', function () {
            return "<?php echo session('rol', 'guest'); ?>";
        });

        // Helper para verificar si el usuario tiene un rol específico
        Blade::directive('customhasrole', function ($role) {
            return "<?php if(session('rol') === {$role}): ?>";
        });

        // Helper para verificar si el usuario es admin
        Blade::directive('customadmin', function () {
            return "<?php if(session('rol') === 'admin'): ?>";
        });

        // Helper para verificar si el usuario es capitán
        Blade::directive('customcapitan', function () {
            return "<?php if(session('rol') === 'capitan'): ?>";
        });

        // Helper para verificar si el usuario es jugador
        Blade::directive('customjugador', function () {
            return "<?php if(session('rol') === 'jugador'): ?>";
        });
    }
}
