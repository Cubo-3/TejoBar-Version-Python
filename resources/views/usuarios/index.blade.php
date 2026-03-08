@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios - TejoBar')

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
<h1 class="mt-4">Gestión de Usuarios 👥</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Usuarios registrados</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Número</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
                                @csrf
                                @method('PUT')
                                <td>{{ $usuario->idPersona }}</td>
                                <td>
                                    <input type="text" name="nombre" value="{{ $usuario->nombre }}" class="form-control">
                                </td>
                                <td>
                                    <input type="email" name="correo" value="{{ $usuario->correo }}" class="form-control">
                                </td>
                                <td>
                                    <select name="rol" class="form-control">
                                        <option value="jugador" {{ $usuario->rol == "jugador" ? "selected" : "" }}>Jugador</option>
                                        <option value="capitan" {{ $usuario->rol == "capitan" ? "selected" : "" }}>Capitán</option>
                                        <option value="admin" {{ $usuario->rol == "admin" ? "selected" : "" }}>Admin</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="numero" value="{{ $usuario->numero }}" class="form-control">
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <form method="POST" action="{{ route('usuarios.destroy', $usuario->idPersona) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger ml-2">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
