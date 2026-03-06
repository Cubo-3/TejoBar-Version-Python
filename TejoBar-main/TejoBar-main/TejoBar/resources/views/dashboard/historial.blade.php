@extends('layouts.dashboard')

@section('title', 'Historial Apartados - TejoBar')

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
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-history mr-2"></i>Historial Apartados
    </h1>
</div>

<!-- Mensajes de éxito/error -->
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

@if(session('rol') === 'admin')
    <!-- ADMIN: Historial completo de apartados -->
    
    <!-- Apartados Pendientes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-clock mr-2"></i>Apartados Pendientes
            </h6>
        </div>
        <div class="card-body">
            @if(isset($apartadosPendientes) && $apartadosPendientes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Usuario</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                                <th>Fecha Apartado</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalPendientes = 0; @endphp
                            @foreach($apartadosPendientes as $apartado)
                                @php
                                    $subtotal = $apartado->producto->precio * $apartado->cantidad;
                                    $totalPendientes += $subtotal;
                                @endphp
                                <tr>
                                    <td>
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $apartado->persona->nombre ?? 'Usuario' }}
                                    </td>
                                    <td>
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $apartado->producto->nombre }}
                                    </td>
                                    <td class="text-center">{{ $apartado->cantidad }}</td>
                                    <td class="text-right">${{ number_format($apartado->producto->precio, 0, ',', '.') }}</td>
                                    <td class="text-right">${{ number_format($subtotal, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $apartado->fechaApartado->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock mr-1"></i>{{ ucfirst($apartado->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <td colspan="4"><strong>Total Pendientes</strong></td>
                                <td class="text-right"><strong>${{ number_format($totalPendientes, 0, ',', '.') }}</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>No hay apartados pendientes</h5>
                    <p class="mb-0">Todos los apartados han sido entregados.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Apartados Entregados -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-check-circle mr-2"></i>Apartados Entregados
            </h6>
        </div>
        <div class="card-body">
            @if(isset($apartadosEntregados) && $apartadosEntregados->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Usuario</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                                <th>Fecha Entrega</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalEntregados = 0; @endphp
                            @foreach($apartadosEntregados as $historial)
                                @php
                                    $subtotal = $historial->precio * $historial->cantidad;
                                    $totalEntregados += $subtotal;
                                @endphp
                                <tr>
                                    <td>
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $historial->persona->nombre ?? 'Usuario' }}
                                    </td>
                                    <td>
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $historial->producto->nombre }}
                                    </td>
                                    <td class="text-center">{{ $historial->cantidad }}</td>
                                    <td class="text-right">${{ number_format($historial->precio, 0, ',', '.') }}</td>
                                    <td class="text-right">${{ number_format($subtotal, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $historial->fechaEntrega->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i>{{ ucfirst($historial->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <td colspan="4"><strong>Total Entregados</strong></td>
                                <td class="text-right"><strong>${{ number_format($totalEntregados, 0, ',', '.') }}</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>No hay apartados entregados</h5>
                    <p class="mb-0">Aún no se han entregado apartados.</p>
                </div>
            @endif
        </div>
    </div>

@else
    <!-- JUGADOR/CAPITAN: Su historial personal de apartados -->
    
    <!-- Mis Apartados Pendientes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-clock mr-2"></i>Mis Apartados Pendientes
            </h6>
        </div>
        <div class="card-body">
            @if(isset($misApartadosPendientes) && $misApartadosPendientes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                                <th>Fecha Apartado</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalPendientes = 0; @endphp
                            @foreach($misApartadosPendientes as $apartado)
                                @php
                                    $subtotal = $apartado->producto->precio * $apartado->cantidad;
                                    $totalPendientes += $subtotal;
                                @endphp
                                <tr>
                                    <td>
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $apartado->producto->nombre }}
                                    </td>
                                    <td class="text-center">{{ $apartado->cantidad }}</td>
                                    <td class="text-right">${{ number_format($apartado->producto->precio, 0, ',', '.') }}</td>
                                    <td class="text-right">${{ number_format($subtotal, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $apartado->fechaApartado->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock mr-1"></i>{{ ucfirst($apartado->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <td colspan="3"><strong>Total Pendientes</strong></td>
                                <td class="text-right"><strong>${{ number_format($totalPendientes, 0, ',', '.') }}</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>No tienes apartados pendientes</h5>
                    <p class="mb-0">Todos tus apartados han sido entregados.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Mis Apartados Entregados -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-check-circle mr-2"></i>Mis Apartados Entregados
            </h6>
        </div>
        <div class="card-body">
            @if(isset($misApartadosEntregados) && $misApartadosEntregados->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                                <th>Fecha Entrega</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalEntregados = 0; @endphp
                            @foreach($misApartadosEntregados as $historial)
                                @php
                                    $subtotal = $historial->precio * $historial->cantidad;
                                    $totalEntregados += $subtotal;
                                @endphp
                                <tr>
                                    <td>
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $historial->producto->nombre }}
                                    </td>
                                    <td class="text-center">{{ $historial->cantidad }}</td>
                                    <td class="text-right">${{ number_format($historial->precio, 0, ',', '.') }}</td>
                                    <td class="text-right">${{ number_format($subtotal, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $historial->fechaEntrega->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i>{{ ucfirst($historial->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <td colspan="3"><strong>Total Entregados</strong></td>
                                <td class="text-right"><strong>${{ number_format($totalEntregados, 0, ',', '.') }}</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>No tienes apartados entregados</h5>
                    <p class="mb-0">Tus apartados entregados aparecerán aquí.</p>
                </div>
            @endif
        </div>
    </div>
@endif

@endsection
