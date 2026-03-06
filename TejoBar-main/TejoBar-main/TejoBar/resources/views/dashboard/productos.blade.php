@extends('layouts.dashboard')

@section('title', 'Productos Apartados - TejoBar')

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
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-shopping-cart mr-2"></i>Productos Apartados
    </h1>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('rol') == "admin")
    <!-- ADMIN: CRUD -->
    <div class="card mb-4">
        <div class="card-header">Registrar nuevo producto</div>
        <div class="card-body">
            <form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
                    </div>
                    <div class="col">
                        <input type="number" min="0" name="stock" class="form-control" placeholder="Stock" required>
                    </div>
                    <div class="col">
                        <input type="number" step="0.01" min="0.01" name="precio" class="form-control" placeholder="Precio" required>
                    </div>
                    <div class="col">
                        <input type="date" name="fechaVencimiento" class="form-control" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col">
                        <input type="file" name="urlImg" class="form-control-file">
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ADMIN: Gestión de Apartados -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">📦 Gestión de Apartados</h6>
        </div>
        <div class="card-body">
            @if(isset($apartados) && $apartados->count() > 0)
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Total</th>
                            <th>Fecha Apartado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($apartados as $apartado)
                            <tr>
                                <td>{{ $apartado->persona->nombre ?? 'Usuario' }}</td>
                                <td>{{ $apartado->producto->nombre }}</td>
                                <td>{{ $apartado->cantidad }}</td>
                                <td>${{ number_format($apartado->producto->precio, 0, ',', '.') }}</td>
                                <td>${{ number_format($apartado->producto->precio * $apartado->cantidad, 0, ',', '.') }}</td>
                                <td>{{ $apartado->fechaApartado->format('d/m/Y H:i') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('apartados.entregar', $apartado) }}" style="display: inline;" onsubmit="return confirm('¿Marcar este apartado como entregado?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Marcar como entregado">
                                            <i class="fas fa-check"></i> Marcar como entregado
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-info">No hay apartados pendientes de entrega.</div>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Productos</h6>
        </div>
        <div class="card-body">
            @if($productos->count() > 0)
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Stock</th>
                            <th>Precio</th>
                            <th>Fecha Vencimiento</th>
                            <th>Imagen</th>
                            <th>Actualizar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                            <tr>
                                <td>{{ $producto->idProducto }}</td>
                                <form method="POST" action="{{ route('productos.update', $producto) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <td>
                                        <input type="text" name="nombre" value="{{ $producto->nombre }}" class="form-control">
                                    </td>
                                    <td>
                                        <input type="number" min="0" name="stock" value="{{ $producto->stock }}" class="form-control">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0.01" name="precio" value="{{ $producto->precio }}" class="form-control">
                                    </td>
                                    <td>
                                        <input type="date" name="fechaVencimiento" value="{{ $producto->fechaVencimiento->format('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="form-control">
                                    </td>
                                    <td>
                                        @if(!empty($producto->urlImg))
                                            <img src="{{ asset('img/productos/' . $producto->urlImg) }}" width="50">
                                        @endif
                                        <input type="file" name="urlImg" class="form-control-file mt-1">
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-save"></i> Actualizar
                                        </button>
                                    </td>
                                </form>
                                <td>
                                    <form method="POST" action="{{ route('productos.destroy', $producto) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning">No hay productos registrados.</div>
            @endif
        </div>
    </div>
@else
    <!-- CAPITAN/JUGADOR: Apartados -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Mis Apartados</h6>
        </div>
        <div class="card-body">
            @if($apartados->count() > 0)
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalGeneral = 0; @endphp
                        @foreach($apartados as $apartado)
                            @php
                                $subtotal = $apartado->producto->precio * $apartado->cantidad;
                                $totalGeneral += $subtotal;
                            @endphp
                            <tr>
                                <td>{{ $apartado->producto->nombre }}</td>
                                <form method="POST" action="{{ route('apartados.update', $apartado) }}">
                                    @csrf
                                    @method('PUT')
                                    <td>
                                        <input type="number" name="cantidad" value="{{ $apartado->cantidad }}" min="1" max="{{ $apartado->producto->stock }}" class="form-control" style="width: 80px;">
                                    </td>
                                    <td>${{ number_format($apartado->producto->precio, 0, ',', '.') }}</td>
                                    <td>${{ number_format($subtotal, 0, ',', '.') }}</td>
                                    <td>{{ $apartado->fechaApartado->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button type="submit" class="btn btn-sm btn-success" title="Actualizar cantidad">
                                            <i class="fas fa-save"></i> Actualizar
                                        </button>
                                    </td>
                                </form>
                                <td>
                                    <form method="POST" action="{{ route('apartados.destroy', $apartado) }}" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este apartado?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar apartado">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Total</strong></td>
                            <td colspan="3"><strong>${{ number_format($totalGeneral, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="alert alert-warning">No tienes productos apartados.</div>
            @endif
        </div>
    </div>
@endif
@endsection
