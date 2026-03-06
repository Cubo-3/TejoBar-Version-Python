@extends('layouts.dashboard')

@section('title', 'Dashboard - TejoBar')

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

.badge-warning {
    background-color: #f6c23e;
    color: #212529;
}

.badge-success {
    background-color: #1cc88a;
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

.border-color: #0056b3;
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
<h1 class="mt-4">Bienvenido {{ session('nombre') }} 👋</h1>
<p>Tu rol es: <strong>{{ ucfirst(session('rol')) }}</strong></p>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- ===================== JUGADOR ===================== -->
@if(session('rol') === "jugador" || session('rol') === "capitan")
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                @php
                    $esCapitanReal = $usuario->jugador && $usuario->jugador->equipos->count() > 0 ? 
                        $usuario->jugador->equipos->first()->jugadores()->where('idJugador', session('idPersona'))->first()->pivot->esCapitan ?? false : false;
                @endphp
                {{ $esCapitanReal ? "Gestión de tu Equipo" : "Próximos Partidos" }}
            </h6>
        </div>
        <div class="card-body">
            @if($usuario->jugador && $usuario->jugador->equipos->count() > 0)
                @php
                    $equipo = $usuario->jugador->equipos->first();
                    // Verificar si realmente es capitán en la base de datos
                    $jugadorEnEquipo = $equipo->jugadores()->where('idJugador', session('idPersona'))->first();
                    $esCapitanReal = $jugadorEnEquipo ? $jugadorEnEquipo->pivot->esCapitan : false;
                @endphp
                
                
                <p>Perteneces al equipo: <strong>{{ $equipo->nombreEquipo }}</strong></p>

                @if(!$esCapitanReal)
                    <form method="POST" action="{{ route('equipos.salirse', $equipo) }}" onsubmit="return confirm('¿Seguro que quieres salir del equipo?');">
                        @csrf
                        <button type="submit" class="btn btn-danger mb-3">
                            <i class="fas fa-sign-out-alt"></i> Salir del equipo
                        </button>
                    </form>
                @endif

                @if($esCapitanReal)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Jugadores de tu equipo</h5>
                        <form method="POST" action="{{ route('equipos.eliminar-completo', $equipo) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" 
                                    onclick="return confirm('⚠️ ADVERTENCIA: Esto eliminará completamente el equipo y todos sus miembros serán removidos. ¿Estás seguro?')">
                                <i class="fas fa-trash-alt mr-1"></i>Eliminar Equipo
                            </button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr><th>ID</th><th>Nombre</th><th>Acción</th></tr>
                            </thead>
                            <tbody>
                                @foreach($equipo->jugadores as $jugador)
                                    <tr>
                                        <td>{{ $jugador->idPersona }}</td>
                                        <td>{{ $jugador->persona->nombre }}</td>
                                        <td>
                                            @if($jugador->idPersona != session('idPersona'))
                                                <form method="POST" action="{{ route('equipos.expulsar-jugador', $equipo) }}" style="display:inline;" onsubmit="return confirm('¿Expulsar a este jugador?');">
                                                    @csrf
                                                    <input type="hidden" name="idJugador" value="{{ $jugador->idPersona }}">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-user-times"></i> Expulsar
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">Tú (Capitán)</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($esCapitanReal)
                    <h5 class="mt-4">Mis Partidos Programados</h5>
                    @if(isset($misPartidos) && $misPartidos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID Torneo</th>
                                        <th>Fecha</th>
                                        <th>Equipo 1</th>
                                        <th>Equipo 2</th>
                                        <th>Cancha</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($misPartidos as $torneo)
                                        <tr>
                                            <td>{{ $torneo->idPartido }}</td>
                                            <td>{{ $torneo->fecha ? \Carbon\Carbon::parse($torneo->fecha)->format('d/m/Y H:i') : 'N/A' }}</td>
                                            <td>{{ $torneo->equipo1 ? \App\Models\Equipo::find($torneo->equipo1)->nombreEquipo ?? 'Equipo ' . $torneo->equipo1 : 'N/A' }}</td>
                                            <td>{{ $torneo->equipo2 ? \App\Models\Equipo::find($torneo->equipo2)->nombreEquipo ?? 'Equipo ' . $torneo->equipo2 : 'N/A' }}</td>
                                            <td>Cancha {{ $torneo->cancha->idCancha ?? 'N/A' }}</td>
                                            <td>
                                                @if($torneo->fecha && \Carbon\Carbon::parse($torneo->fecha)->isFuture())
                                                    <form method="POST" action="{{ route('torneos.destroy', $torneo) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este torneo?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash mr-1"></i>Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">Ya pasó</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No tienes partidos programados.</p>
                    @endif
                @else
                    <h5 class="mt-4">Próximos Partidos</h5>
                    @if($partidos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID Torneo</th>
                                        <th>Fecha</th>
                                        <th>Equipo 1</th>
                                        <th>Equipo 2</th>
                                        <th>Cancha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($partidos as $torneo)
                                        <tr>
                                            <td>{{ $torneo->idPartido }}</td>
                                            <td>{{ $torneo->fecha ? \Carbon\Carbon::parse($torneo->fecha)->format('d/m/Y H:i') : 'N/A' }}</td>
                                            <td>{{ $torneo->equipo1 ? \App\Models\Equipo::find($torneo->equipo1)->nombreEquipo ?? 'Equipo ' . $torneo->equipo1 : 'N/A' }}</td>
                                            <td>{{ $torneo->equipo2 ? \App\Models\Equipo::find($torneo->equipo2)->nombreEquipo ?? 'Equipo ' . $torneo->equipo2 : 'N/A' }}</td>
                                            <td>Cancha {{ $torneo->cancha->idCancha ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p>No hay partidos programados.</p>
                    @endif
                @endif
            @else
                <div class="alert alert-warning">No estás en un equipo aún.</div>
            @endif
        </div>
    </div>
@endif
<!-- ===================== /JUGADOR & CAPITAN ===================== -->

@if(session('rol') === "admin")
    <!-- Estadísticas para admin -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Productos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProductos ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Apartados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalApartados ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Equipos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalEquipos ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Canchas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCanchas ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-futbol fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
