<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TejoBar')</title>
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header>
        <a href="{{ route('home') }}" class="logo">TejoBar</a>
        <nav>
            <ul class="menu-top">
                <li><a href="{{ route('home') }}">🏠Inicio</a></li>
                <li><a href="{{ route('torneos.index') }}">🥇Partidos</a></li>
                <li><a href="{{ route('productos.index') }}">🍻Comida y Bebida</a></li>
                @customauth
                    <li><a href="{{ route('dashboard') }}">📊Dashboard</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="border: none; background: none; color: inherit; text-decoration: none; cursor: pointer;">
                                🚪Cerrar Sesión
                            </button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login', ['ref' => request()->fullUrl()]) }}">🔓Iniciar Sesión</a></li>
                @endcustomauth
            </ul>
        </nav>
    </header>

    <!-- Contenido principal -->
    <div class="container">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 TejoBar. Todos los derechos reservados.</p>
    </footer>

    @stack('scripts')
</body>
</html>
