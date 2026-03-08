<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title', 'TejoBar - Dashboard')</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @stack('styles')
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-light border-right" id="sidebar-wrapper">
        <div class="sidebar-heading">Tejobar</div>
        <div class="list-group list-group-flush">
            <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action bg-light {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-fw fa-tachometer-alt mr-2"></i>Dashboard
            </a>

            @if(session('rol') === 'capitan')
                <a href="{{ route('dashboard.productos') }}" class="list-group-item list-group-item-action bg-light {{ request()->routeIs('dashboard.productos') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-shopping-cart mr-2"></i>Productos Apartados
                </a>
                <a href="{{ route('dashboard.historial') }}" class="list-group-item list-group-item-action bg-light {{ request()->routeIs('dashboard.historial') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-history mr-2"></i>Historial Apartados
                </a>
            @elseif(session('rol') === 'jugador')
                <a href="{{ route('dashboard.productos') }}" class="list-group-item list-group-item-action bg-light {{ request()->routeIs('dashboard.productos') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-shopping-cart mr-2"></i>Productos Apartados
                </a>
                <a href="{{ route('dashboard.historial') }}" class="list-group-item list-group-item-action bg-light {{ request()->routeIs('dashboard.historial') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-history mr-2"></i>Historial Apartados
                </a>
                <a href="{{ route('equipos.disponibles') }}" class="list-group-item list-group-item-action bg-light {{ request()->routeIs('equipos.disponibles') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-users mr-2"></i>Equipos
                </a>
            @elseif(session('rol') === 'admin')
                <a href="{{ route('equipos.index') }}" class="list-group-item list-group-item-action bg-light {{ request()->routeIs('equipos.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-users mr-2"></i>Equipos
                </a>

                <a href="{{ route('dashboard.productos') }}" class="list-group-item list-group-item-action bg-light {{ request()->routeIs('dashboard.productos') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-shopping-cart mr-2"></i>Productos Apartados
                </a>

                <a href="{{ route('dashboard.historial') }}" class="list-group-item list-group-item-action bg-light {{ request()->routeIs('dashboard.historial') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-history mr-2"></i>Historial Apartados
                </a>

                <a href="{{ route('usuarios.index') }}" class="list-group-item list-group-item-action bg-light {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-user-cog mr-2"></i>Editar usuarios
                </a>
            @endif

            <a href="{{ route('home') }}" class="list-group-item list-group-item-action bg-light">
                <i class="fas fa-fw fa-home mr-2"></i>Inicio
            </a>
        </div>
    </div>

    <!-- /Sidebar -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <button class="btn btn-primary" id="menu-toggle"><i class="fas fa-bars"></i></button>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                            <i class="fas fa-user-circle mr-1"></i>{{ ucfirst(session('rol')) }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#"><i class="fas fa-user mr-1"></i> Perfil</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container-fluid">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("body").toggleClass("sb-sidenav-toggled");
    });
</script>
@stack('scripts')
</body>
</html>
