@extends('layouts.dashboard')

@section('title', 'Equipos Disponibles - TejoBar')

@push('styles')
<style>
body {
    overflow-x: hidden;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

#wrapper {
    display: flex;
}

#sidebar-wrapper {
    min-height: 100vh;
    margin-left: -15rem;
    transition: margin .25s ease-out;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.list-group-item.active{
    color: #333;
}

#sidebar-wrapper .sidebar-heading {
    padding: 0.875rem 1.25rem;
    font-size: 1.3rem;
    font-weight: bold;
    color: #333;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

#sidebar-wrapper .list-group {
    width: 15rem;
}

#sidebar-wrapper .list-group-item {
    border: none;
    padding: 1rem 1.25rem;
    transition: all 0.3s ease;
}

#sidebar-wrapper .list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

#sidebar-wrapper .list-group-item.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

#sidebar-wrapper .list-group-item .fas {
    font-size: 1.1rem;
}

#page-content-wrapper {
    min-width: 100vw;
}

body.sb-sidenav-toggled #wrapper #sidebar-wrapper {
    margin-left: 0;
}

@media (min-width: 768px) {
    #sidebar-wrapper {
        margin-left: 0;
    }

    #page-content-wrapper {
        min-width: 0;
        width: 100%;
    }

    body.sb-sidenav-toggled #wrapper #sidebar-wrapper {
        margin-left: -15rem;
    }
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #5a5c69;
}

.alert {
    border: none;
    border-radius: 0.35rem;
}

.badge {
    font-size: 0.75rem;
    font-weight: 600;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.mb-0 {
    margin-bottom: 0 !important;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}

.d-sm-flex {
    display: flex !important;
}

.align-items-center {
    align-items: center !important;
}

.justify-content-between {
    justify-content: space-between !important;
}

.mr-2 {
    margin-right: 0.5rem !important;
}

.h3 {
    font-size: 1.75rem;
}

.btn {
    border-radius: 0.35rem;
    font-weight: 600;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.2rem;
}

.btn-success {
    background-color: #1cc88a;
    border-color: #1cc88a;
}

.btn-success:hover {
    background-color: #17a673;
    border-color: #17a673;
}

.btn-danger {
    background-color: #e74a3b;
    border-color: #e74a3b;
}

.btn-danger:hover {
    background-color: #c0392b;
    border-color: #c0392b;
}

.btn-warning {
    background-color: #f6c23e;
    border-color: #f6c23e;
}

.btn-warning:hover {
    background-color: #dda20a;
    border-color: #dda20a;
}

.badge-warning {
    background-color: #f6c23e;
    color: #212529;
}

.badge-success {
    background-color: #1cc88a;
    color: #fff;
}

.badge-primary {
    background-color: #4e73df;
    color: #fff;
}

.text-warning {
    color: #f6c23e !important;
}

.text-success {
    color: #1cc88a !important;
}

.text-primary {
    color: #4e73df !important;
}

.list-group-item.active {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: white !important;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">
        <i class="fas fa-users mr-2"></i>Equipos Disponibles
    </h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Estado del jugador actual -->
    @if($jugadorActual)
        <div class="alert alert-info">
            <h5 class="mb-0">
                <i class="fas fa-user mr-2"></i>Estado actual: 
                @if($jugadorActual->equipos->count() > 0)
                    @foreach($jugadorActual->equipos as $equipo)
                        <span class="badge badge-success">
                            {{ $equipo->nombreEquipo }}
                            @if($equipo->pivot->esCapitan)
                                <i class="fas fa-crown ml-1"></i> (Capitán)
                            @else
                                <i class="fas fa-user ml-1"></i> (Jugador)
                            @endif
                        </span>
                    @endforeach
                @else
                    <span class="badge badge-warning">
                        <i class="fas fa-user-times mr-1"></i>Sin equipo
                    </span>
                @endif
            </h5>
        </div>
    @endif

    <!-- Botón para crear equipo -->
    @if(!$jugadorActual || $jugadorActual->equipos->count() == 0)
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ route('equipos.mostrar-crear') }}" class="btn btn-warning">
                    <i class="fas fa-plus mr-2"></i>Crear Nuevo Equipo
                </a>
            </div>
        </div>
    @endif

    <!-- Lista de equipos -->
    <div class="row">
        @forelse($equipos as $equipo)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users mr-2"></i>{{ $equipo->nombreEquipo }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge badge-primary">
                                <i class="fas fa-users mr-1"></i>{{ $equipo->jugadores_count }} jugador(es)
                            </span>
                        </div>

                        @if($equipo->jugadores->count() > 0)
                            <h6 class="text-muted mb-2">Miembros del equipo:</h6>
                            <ul class="list-unstyled">
                                @foreach($equipo->jugadores as $jugador)
                                    <li class="mb-1">
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $jugador->persona->nombre }} {{ $jugador->persona->apellido }}
                                        @if($jugador->pivot->esCapitan)
                                            <span class="badge badge-warning ml-1">
                                                <i class="fas fa-crown"></i> Capitán
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted mb-3">
                                <i class="fas fa-info-circle mr-1"></i>Este equipo no tiene miembros aún.
                            </p>
                        @endif

                        <!-- Botones de acción -->
                        <div class="mt-3">
                            @if($jugadorActual && $jugadorActual->equipos->contains($equipo))
                                <!-- Ya está en este equipo -->
                                @if($jugadorActual->equipos->where('id', $equipo->id)->first()->pivot->esCapitan)
                                    <span class="badge badge-warning">
                                        <i class="fas fa-crown mr-1"></i>Eres el capitán
                                    </span>
                                    <p class="text-muted mt-2 mb-0">
                                        <small><i class="fas fa-info-circle mr-1"></i>Gestiona tu equipo desde el Dashboard</small>
                                    </p>
                                @else
                                    <form method="POST" action="{{ route('equipos.salirse', $equipo) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('¿Estás seguro de que quieres salirte de este equipo?')">
                                            <i class="fas fa-sign-out-alt mr-1"></i>Salirse
                                        </button>
                                    </form>
                                @endif
                            @elseif($jugadorActual && $jugadorActual->equipos->count() > 0)
                                <!-- Ya está en otro equipo -->
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-lock mr-1"></i>Ya estás en un equipo
                                </button>
                            @else
                                <!-- Puede unirse -->
                                <form method="POST" action="{{ route('equipos.unirse', $equipo) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-user-plus mr-1"></i>Unirse
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay equipos disponibles</h5>
                        <p class="text-muted">Sé el primero en crear un equipo.</p>
                        @if(!$jugadorActual || $jugadorActual->equipos->count() == 0)
                            <a href="{{ route('equipos.mostrar-crear') }}" class="btn btn-warning">
                                <i class="fas fa-plus mr-2"></i>Crear Primer Equipo
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
