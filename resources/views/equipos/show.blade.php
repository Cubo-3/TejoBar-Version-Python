@extends('layouts.dashboard')

@section('title', 'Detalles del Equipo - TejoBar')

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

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
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

.list-group-item.active {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: white !important;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-users mr-2"></i>{{ $equipo->nombreEquipo }}
    </h1>
    <div>
        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Volver a Equipos
        </a>
        <a href="{{ route('equipos.edit', $equipo) }}" class="btn btn-primary">
            <i class="fas fa-edit mr-1"></i>Editar Equipo
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<!-- Información del Equipo -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jugadores</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $equipo->jugadores->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Torneos Como Equipo 1</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $equipo->torneosComoEquipo1->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Torneos Como Equipo 2</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $equipo->torneosComoEquipo2->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-medal fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Torneos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $equipo->torneosComoEquipo1->count() + $equipo->torneosComoEquipo2->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-gamepad fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Jugadores del Equipo -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users mr-2"></i>Jugadores del Equipo
        </h6>
        @if(session('rol') === 'admin')
            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#agregarJugadorModal">
                <i class="fas fa-plus mr-1"></i>Agregar Jugador
            </button>
        @endif
    </div>
    <div class="card-body">
        @if($equipo->jugadores->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipo->jugadores as $jugador)
                            <tr>
                                <td>{{ $jugador->idPersona }}</td>
                                <td>
                                    {{ $jugador->persona->nombre }}
                                    @if($jugador->pivot->esCapitan)
                                        <span class="badge badge-warning ml-2">Capitán</span>
                                    @endif
                                </td>
                                <td>{{ $jugador->persona->correo }}</td>
                                <td>{{ $jugador->persona->numero }}</td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst($jugador->persona->rol) }}</span>
                                </td>
                                <td>
                                    @if(session('rol') === 'admin')
                                        @if(!$jugador->pivot->esCapitan)
                                            <form method="POST" action="{{ route('equipos.remover-jugador', $equipo) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de remover a este jugador del equipo?');">
                                                @csrf
                                                <input type="hidden" name="idJugador" value="{{ $jugador->idPersona }}">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-user-times mr-1"></i>Remover
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">Capitán</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>Este equipo no tiene jugadores asignados.
            </div>
        @endif
    </div>
</div>

<!-- Torneos del Equipo -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-trophy mr-2"></i>Torneos del Equipo
        </h6>
    </div>
    <div class="card-body">
        @php
            $todosTorneos = $equipo->torneosComoEquipo1->concat($equipo->torneosComoEquipo2);
        @endphp
        
        @if($todosTorneos->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID Torneo</th>
                            <th>Fecha</th>
                            <th>Equipo 1</th>
                            <th>Equipo 2</th>
                            <th>Cancha</th>
                            <th>Posición</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todosTorneos as $torneo)
                            <tr>
                                <td>{{ $torneo->idPartido }}</td>
                                <td>{{ $torneo->fecha ? \Carbon\Carbon::parse($torneo->fecha)->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>
                                    @if($torneo->equipo1)
                                        @php $equipo1 = \App\Models\Equipo::find($torneo->equipo1) @endphp
                                        {{ $equipo1 ? $equipo1->nombreEquipo : 'Equipo ' . $torneo->equipo1 }}
                                        @if($torneo->equipo1 == $equipo->idEquipo)
                                            <span class="badge badge-primary ml-1">Este Equipo</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($torneo->equipo2)
                                        @php $equipo2 = \App\Models\Equipo::find($torneo->equipo2) @endphp
                                        {{ $equipo2 ? $equipo2->nombreEquipo : 'Equipo ' . $torneo->equipo2 }}
                                        @if($torneo->equipo2 == $equipo->idEquipo)
                                            <span class="badge badge-primary ml-1">Este Equipo</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>Cancha {{ $torneo->cancha->idCancha ?? 'N/A' }}</td>
                                <td>
                                    @if($torneo->equipo1 == $equipo->idEquipo)
                                        <span class="badge badge-success">Equipo 1</span>
                                    @elseif($torneo->equipo2 == $equipo->idEquipo)
                                        <span class="badge badge-info">Equipo 2</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>Este equipo no tiene torneos programados.
            </div>
        @endif
    </div>
</div>

<!-- Modal para Agregar Jugador -->
@if(session('rol') === 'admin')
    <div class="modal fade" id="agregarJugadorModal" tabindex="-1" role="dialog" aria-labelledby="agregarJugadorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarJugadorModalLabel">Agregar Jugador al Equipo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('equipos.agregar-jugador', $equipo) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="idJugador">Seleccionar Jugador:</label>
                            <select class="form-control" id="idJugador" name="idJugador" required>
                                <option value="">Selecciona un jugador...</option>
                                @foreach($jugadoresDisponibles as $jugador)
                                    <option value="{{ $jugador->idPersona }}">{{ $jugador->persona->nombre }} ({{ $jugador->persona->correo }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="esCapitan" id="esCapitan" value="1">
                            <label class="form-check-label" for="esCapitan">
                                Hacer capitán del equipo
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Agregar Jugador</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection


