@extends('layouts.dashboard')

@section('title', 'Editar Equipo - TejoBar')

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
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-edit mr-2"></i>Editar Equipo: {{ $equipo->nombreEquipo }}
    </h1>
    <div>
        <a href="{{ route('equipos.show', $equipo) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>Volver al Equipo
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

<!-- Formulario de Edición -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit mr-2"></i>Información del Equipo
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('equipos.update', $equipo) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="nombreEquipo">Nombre del Equipo:</label>
                        <input type="text" 
                               class="form-control @error('nombreEquipo') is-invalid @enderror" 
                               id="nombreEquipo" 
                               name="nombreEquipo" 
                               value="{{ old('nombreEquipo', $equipo->nombreEquipo) }}" 
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
                            <i class="fas fa-save mr-1"></i>Actualizar Equipo
                        </button>
                        <a href="{{ route('equipos.show', $equipo) }}" class="btn btn-secondary ml-2">
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
                    <i class="fas fa-info-circle mr-2"></i>Información del Equipo
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>ID del Equipo:</strong>
                    <span class="badge badge-primary">{{ $equipo->idEquipo }}</span>
                </div>
                
                <div class="mb-3">
                    <strong>Total de Jugadores:</strong>
                    <span class="badge badge-success">{{ $equipo->jugadores->count() }}</span>
                </div>
                
                <div class="mb-3">
                    <strong>Total de Torneos:</strong>
                    <span class="badge badge-info">{{ $equipo->torneosComoEquipo1->count() + $equipo->torneosComoEquipo2->count() }}</span>
                </div>
                
                <div class="mb-3">
                    <strong>Estado:</strong>
                    @if($equipo->jugadores->count() > 0)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-warning">Sin Jugadores</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Acciones Adicionales -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-cogs mr-2"></i>Acciones
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('equipos.show', $equipo) }}" class="btn btn-outline-primary">
                        <i class="fas fa-eye mr-1"></i>Ver Detalles
                    </a>
                    
                    @if($equipo->jugadores->count() == 0 && $equipo->torneosComoEquipo1->count() == 0 && $equipo->torneosComoEquipo2->count() == 0)
                        <form method="POST" action="{{ route('equipos.destroy', $equipo) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este equipo? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash mr-1"></i>Eliminar Equipo
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-outline-danger w-100" disabled title="No se puede eliminar un equipo con jugadores o torneos">
                            <i class="fas fa-trash mr-1"></i>Eliminar Equipo
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


