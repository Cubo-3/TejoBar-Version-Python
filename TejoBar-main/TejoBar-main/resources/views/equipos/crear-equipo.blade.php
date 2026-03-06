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

.btn-warning {
    background-color: #f6c23e;
    border-color: #f6c23e;
}

.btn-warning:hover {
    background-color: #dda20a;
    border-color: #dda20a;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #545b62;
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

.form-control {
    border-radius: 0.35rem;
    border: 1px solid #d1d3e2;
}

.form-control:focus {
    border-color: #bac8f3;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-group label {
    font-weight: 600;
    color: #5a5c69;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">
        <i class="fas fa-plus-circle mr-2"></i>Crear Nuevo Equipo
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

    <div class="row">
        <!-- Información sobre crear equipo -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>¿Qué significa crear un equipo?</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Te convertirás en el <strong>capitán</strong> del equipo</li>
                        <li>Podrás invitar a otros jugadores</li>
                        <li>Gestionarás los miembros del equipo</li>
                        <li>Representarás al equipo en partidos</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-crown mr-2"></i>Responsabilidades del capitán</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Gestionar miembros del equipo</li>
                        <li>Coordinar partidos</li>
                        <li>Tomar decisiones importantes</li>
                        <li>Mantener la disciplina del equipo</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Formulario de creación -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users mr-2"></i>Información del Equipo
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('equipos.crear-equipo') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="nombreEquipo">
                                <i class="fas fa-tag mr-1"></i>Nombre del Equipo
                            </label>
                            <input type="text" 
                                   class="form-control @error('nombreEquipo') is-invalid @enderror" 
                                   id="nombreEquipo" 
                                   name="nombreEquipo" 
                                   value="{{ old('nombreEquipo') }}" 
                                   placeholder="Ej: Los Tigres, Equipo Alpha, etc."
                                   required>
                            @error('nombreEquipo')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted">
                                Elige un nombre único y representativo para tu equipo.
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="alert alert-info">
                                <i class="fas fa-lightbulb mr-2"></i>
                                <strong>Consejo:</strong> Elige un nombre que represente la identidad de tu equipo. 
                                Una vez creado, podrás invitar a otros jugadores a unirse.
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-plus mr-2"></i>Crear Equipo y Convertirme en Capitán
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Botón para volver -->
            <div class="text-center mt-3">
                <a href="{{ route('equipos.disponibles') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a Equipos Disponibles
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
