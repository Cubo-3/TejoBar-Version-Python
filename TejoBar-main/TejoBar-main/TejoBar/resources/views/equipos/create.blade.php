@extends('layouts.dashboard')

@section('title', 'Crear Equipo - TejoBar')

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

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
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

.form-control {
    border-radius: 0.35rem;
    border: 1px solid #d1d3e2;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-group label {
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.5rem;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-plus mr-2"></i>Crear Nuevo Equipo
    </h1>
    <div>
        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Volver a Equipos
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

<!-- Estadísticas Rápidas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Equipos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Equipo::count() }}</div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Equipos Activos</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Equipo::whereHas('jugadores')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sin Jugadores</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Equipo::whereDoesntHave('jugadores')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-times fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Jugadores</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Jugador::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario de Creación -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-plus mr-2"></i>Información del Nuevo Equipo
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('equipos.store') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="nombreEquipo">Nombre del Equipo: <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nombreEquipo') is-invalid @enderror" 
                               id="nombreEquipo" 
                               name="nombreEquipo" 
                               value="{{ old('nombreEquipo') }}" 
                               required 
                               maxlength="100"
                               placeholder="Ingresa el nombre del equipo">
                        @error('nombreEquipo')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>El nombre debe ser único y tener máximo 100 caracteres.
                        </small>
                    </div>

                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Crear Equipo
                        </button>
                        <a href="{{ route('equipos.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-times mr-1"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-2"></i>Información Importante
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb mr-2"></i>
                    <strong>Consejo:</strong> Elige un nombre descriptivo y único para tu equipo.
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Nota:</strong> Una vez creado el equipo, podrás agregar jugadores desde la página de detalles.
                </div>

                <h6 class="font-weight-bold text-primary mb-3">
                    <i class="fas fa-list mr-2"></i>Requisitos:
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Nombre único en el sistema
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Máximo 100 caracteres
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success mr-2"></i>
                        Solo caracteres alfanuméricos
                    </li>
                </ul>
            </div>
        </div>

        <!-- Equipos Recientes -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clock mr-2"></i>Equipos Recientes
                </h6>
            </div>
            <div class="card-body">
                @php
                    $equiposRecientes = \App\Models\Equipo::orderBy('created_at', 'desc')->limit(5)->get();
                @endphp
                
                @if($equiposRecientes->count() > 0)
                    @foreach($equiposRecientes as $equipo)
                        <div class="d-flex align-items-center mb-2">
                            <div class="mr-3">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $equipo->nombreEquipo }}</div>
                                <small class="text-muted">
                                    {{ $equipo->created_at->format('d/m/Y') }}
                                    <span class="badge badge-info ml-1">{{ $equipo->jugadores->count() }} jugadores</span>
                                </small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <p>No hay equipos creados aún.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


