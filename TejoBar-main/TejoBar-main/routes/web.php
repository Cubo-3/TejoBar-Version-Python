<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ApartadoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\CanchaController;
use App\Http\Controllers\ReporteController;

// Página principal
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas públicas
Route::get('/torneos', [TorneoController::class, 'index'])->name('torneos.index');
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');

// Ruta POST para dashboard (login directo)
Route::post('/dashboard', [AuthController::class, 'loginToDashboard'])->name('dashboard.login');

// Rutas protegidas
Route::middleware([\App\Http\Middleware\AuthMiddleware::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Apartados
    Route::post('/apartados', [ApartadoController::class, 'store'])->name('apartados.store');
    Route::get('/apartados', [ApartadoController::class, 'index'])->name('apartados.index');
    Route::get('/mis-apartados', [ApartadoController::class, 'misApartados'])->name('apartados.mis-apartados');
    Route::get('/apartados/{apartado}', [ApartadoController::class, 'show'])->name('apartados.show');
    Route::patch('/apartados/{apartado}/confirmar', [ApartadoController::class, 'confirmar'])->name('apartados.confirmar');
    Route::patch('/apartados/{apartado}/entregar', [ApartadoController::class, 'entregar'])->name('apartados.entregar');
    Route::put('/apartados/{apartado}', [ApartadoController::class, 'update'])->name('apartados.update');
    Route::delete('/apartados/{apartado}', [ApartadoController::class, 'destroy'])->name('apartados.destroy');
    Route::delete('/apartados/{apartado}/cancelar', [ApartadoController::class, 'cancelar'])->name('apartados.cancelar');
    
    // Productos (CRUD para admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    });
    
    // Compras
    Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
    Route::get('/mis-compras', [CompraController::class, 'misCompras'])->name('compras.mis-compras');
    Route::get('/compras/create', [CompraController::class, 'create'])->name('compras.create');
    Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
    Route::get('/compras/{compra}', [CompraController::class, 'show'])->name('compras.show');
    Route::get('/compras/{compra}/edit', [CompraController::class, 'edit'])->name('compras.edit');
    Route::put('/compras/{compra}', [CompraController::class, 'update'])->name('compras.update');
    Route::delete('/compras/{compra}', [CompraController::class, 'destroy'])->name('compras.destroy');
    Route::post('/compras/procesar-apartado', [CompraController::class, 'procesarDesdeApartado'])->name('compras.procesar-apartado');
    
    // Jugadores
    Route::get('/jugadores', [JugadorController::class, 'index'])->name('jugadores.index');
    Route::get('/jugadores/create', [JugadorController::class, 'create'])->name('jugadores.create');
    Route::post('/jugadores', [JugadorController::class, 'store'])->name('jugadores.store');
    Route::get('/jugadores/{jugador}', [JugadorController::class, 'show'])->name('jugadores.show');
    Route::get('/jugadores/{jugador}/edit', [JugadorController::class, 'edit'])->name('jugadores.edit');
    Route::put('/jugadores/{jugador}', [JugadorController::class, 'update'])->name('jugadores.update');
    Route::delete('/jugadores/{jugador}', [JugadorController::class, 'destroy'])->name('jugadores.destroy');
    Route::post('/jugadores/{jugador}/asignar-equipo', [JugadorController::class, 'asignarEquipo'])->name('jugadores.asignar-equipo');
    Route::post('/jugadores/{jugador}/remover-equipo', [JugadorController::class, 'removerEquipo'])->name('jugadores.remover-equipo');
    Route::post('/equipos/{equipo}/expulsar-jugador', [EquipoController::class, 'expulsarJugador'])->name('equipos.expulsar-jugador');
    Route::patch('/jugadores/{jugador}/toggle-estado', [JugadorController::class, 'toggleEstado'])->name('jugadores.toggle-estado');
    
    // Equipos
    Route::get('/equipos', [EquipoController::class, 'index'])->name('equipos.index');
    Route::get('/equipos/create', [EquipoController::class, 'create'])->name('equipos.create');
    Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');
    Route::get('/equipos/{equipo}', [EquipoController::class, 'show'])->name('equipos.show');
    Route::get('/equipos/{equipo}/edit', [EquipoController::class, 'edit'])->name('equipos.edit');
    Route::put('/equipos/{equipo}', [EquipoController::class, 'update'])->name('equipos.update');
    Route::delete('/equipos/{equipo}', [EquipoController::class, 'destroy'])->name('equipos.destroy');
    Route::post('/equipos/{equipo}/agregar-jugador', [EquipoController::class, 'agregarJugador'])->name('equipos.agregar-jugador');
    Route::post('/equipos/{equipo}/remover-jugador', [EquipoController::class, 'removerJugador'])->name('equipos.remover-jugador');
    Route::post('/equipos/{equipo}/cambiar-capitan', [EquipoController::class, 'cambiarCapitan'])->name('equipos.cambiar-capitan');
    
    // Equipos para jugadores
    Route::get('/equipos-disponibles', [EquipoController::class, 'disponibles'])->name('equipos.disponibles');
    Route::post('/equipos/{equipo}/unirse', [EquipoController::class, 'unirse'])->name('equipos.unirse');
    Route::post('/equipos/{equipo}/salirse', [EquipoController::class, 'salirse'])->name('equipos.salirse');
    Route::get('/crear-equipo', [EquipoController::class, 'mostrarCrearEquipo'])->name('equipos.mostrar-crear');
    Route::post('/crear-equipo', [EquipoController::class, 'crearEquipo'])->name('equipos.crear-equipo');
    Route::delete('/equipos/{equipo}/eliminar-completo', [EquipoController::class, 'eliminarEquipoCompleto'])->name('equipos.eliminar-completo');
    
    // Partidos
    Route::get('/partidos', [PartidoController::class, 'index'])->name('partidos.index');
    Route::get('/partidos/create', [PartidoController::class, 'create'])->name('partidos.create');
    Route::post('/partidos', [PartidoController::class, 'store'])->name('partidos.store');
    Route::get('/partidos/{partido}', [PartidoController::class, 'show'])->name('partidos.show');
    Route::get('/partidos/{partido}/edit', [PartidoController::class, 'edit'])->name('partidos.edit');
    Route::put('/partidos/{partido}', [PartidoController::class, 'update'])->name('partidos.update');
    Route::delete('/partidos/{partido}', [PartidoController::class, 'destroy'])->name('partidos.destroy');
    Route::patch('/partidos/{partido}/confirmar', [PartidoController::class, 'confirmar'])->name('partidos.confirmar');
    Route::patch('/partidos/{partido}/cancelar', [PartidoController::class, 'cancelar'])->name('partidos.cancelar');
    
    // Torneos
    Route::get('/torneos/create', [TorneoController::class, 'create'])->name('torneos.create');
    Route::post('/torneos', [TorneoController::class, 'store'])->name('torneos.store');
    Route::get('/torneos/{torneo}', [TorneoController::class, 'show'])->name('torneos.show');
    Route::get('/torneos/{torneo}/edit', [TorneoController::class, 'edit'])->name('torneos.edit');
    Route::put('/torneos/{torneo}', [TorneoController::class, 'update'])->name('torneos.update');
    Route::delete('/torneos/{torneo}', [TorneoController::class, 'destroy'])->name('torneos.destroy');
    
    // Canchas
    Route::get('/canchas', [CanchaController::class, 'index'])->name('canchas.index');
    Route::get('/canchas/create', [CanchaController::class, 'create'])->name('canchas.create');
    Route::post('/canchas', [CanchaController::class, 'store'])->name('canchas.store');
    Route::get('/canchas/{cancha}', [CanchaController::class, 'show'])->name('canchas.show');
    Route::get('/canchas/{cancha}/edit', [CanchaController::class, 'edit'])->name('canchas.edit');
    Route::put('/canchas/{cancha}', [CanchaController::class, 'update'])->name('canchas.update');
    Route::delete('/canchas/{cancha}', [CanchaController::class, 'destroy'])->name('canchas.destroy');
    Route::patch('/canchas/{cancha}/toggle-estado', [CanchaController::class, 'cambiarEstado'])->name('canchas.toggle-estado');
    Route::patch('/canchas/{cancha}/disponibilidad', [CanchaController::class, 'cambiarDisponibilidad'])->name('canchas.cambiar-disponibilidad');
    
    // Usuarios (solo admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/usuarios', [AuthController::class, 'index'])->name('usuarios.index');
        Route::put('/usuarios/{usuario}', [AuthController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [AuthController::class, 'destroy'])->name('usuarios.destroy');
    });
    
    // Dashboard específico por rol
    Route::get('/dashboard/productos', [DashboardController::class, 'productos'])->name('dashboard.productos');
    Route::get('/dashboard/historial', [DashboardController::class, 'historial'])->name('dashboard.historial');
    
    // Reportes (solo admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dashboard/reportes/productos-csv', [ReporteController::class, 'exportarProductosCSV'])->name('dashboard.reportes.productos.excel');
    });
});

// Rutas API
Route::prefix('api')->group(function () {
    // Estadísticas
    Route::get('/estadisticas', [DashboardController::class, 'estadisticas'])->name('api.estadisticas');
    Route::get('/actividad-reciente', [DashboardController::class, 'actividadReciente'])->name('api.actividad-reciente');
    
    // Productos
    Route::get('/productos/disponibles', [ProductoController::class, 'disponibles'])->name('api.productos.disponibles');
    Route::get('/productos/proximos-vencer', [ProductoController::class, 'proximosAVencer'])->name('api.productos.proximos-vencer');
    Route::get('/productos/search', [ProductoController::class, 'search'])->name('api.productos.search');
    
    // Apartados
    Route::get('/apartados/pendientes', [ApartadoController::class, 'pendientes'])->name('api.apartados.pendientes');
    Route::get('/apartados/comprados', [ApartadoController::class, 'comprados'])->name('api.apartados.comprados');
    Route::get('/apartados/por-persona/{idPersona}', [ApartadoController::class, 'porPersona'])->name('api.apartados.por-persona');
    Route::get('/apartados/por-producto/{idProducto}', [ApartadoController::class, 'porProducto'])->name('api.apartados.por-producto');
    
    // Compras
    Route::get('/compras/por-fecha', [CompraController::class, 'porFecha'])->name('api.compras.por-fecha');
    Route::get('/compras/por-rango-fechas', [CompraController::class, 'porRangoFechas'])->name('api.compras.por-rango-fechas');
    Route::get('/compras/con-total-mayor', [CompraController::class, 'conTotalMayorA'])->name('api.compras.con-total-mayor');
    Route::get('/compras/estadisticas', [CompraController::class, 'estadisticas'])->name('api.compras.estadisticas');
    Route::get('/compras/reporte-ventas', [CompraController::class, 'reporteVentas'])->name('api.compras.reporte-ventas');
    
    // Jugadores
    Route::get('/jugadores/activos', [JugadorController::class, 'activos'])->name('api.jugadores.activos');
    Route::get('/jugadores/inactivos', [JugadorController::class, 'inactivos'])->name('api.jugadores.inactivos');
    Route::get('/jugadores/sin-equipo', [JugadorController::class, 'sinEquipo'])->name('api.jugadores.sin-equipo');
    Route::get('/jugadores/capitanes', [JugadorController::class, 'capitanes'])->name('api.jugadores.capitanes');
    Route::get('/jugadores/por-equipo/{idEquipo}', [JugadorController::class, 'porEquipo'])->name('api.jugadores.por-equipo');
    Route::get('/jugadores/search', [JugadorController::class, 'search'])->name('api.jugadores.search');
    Route::get('/jugadores/estadisticas', [JugadorController::class, 'estadisticas'])->name('api.jugadores.estadisticas');
    
    // Equipos
    Route::get('/equipos/con-jugadores', [EquipoController::class, 'conJugadores'])->name('api.equipos.con-jugadores');
    Route::get('/equipos/sin-jugadores', [EquipoController::class, 'sinJugadores'])->name('api.equipos.sin-jugadores');
    Route::get('/equipos/con-torneos', [EquipoController::class, 'conTorneos'])->name('api.equipos.con-torneos');
    Route::get('/equipos/search', [EquipoController::class, 'search'])->name('api.equipos.search');
    Route::get('/equipos/estadisticas', [EquipoController::class, 'estadisticas'])->name('api.equipos.estadisticas');
    Route::get('/equipos/{equipo}/historial-torneos', [EquipoController::class, 'historialTorneos'])->name('api.equipos.historial-torneos');
    
    // Partidos
    Route::get('/partidos/por-fecha', [PartidoController::class, 'porFecha'])->name('api.partidos.por-fecha');
    Route::get('/partidos/por-cancha', [PartidoController::class, 'porCancha'])->name('api.partidos.por-cancha');
    Route::get('/partidos/confirmados', [PartidoController::class, 'confirmados'])->name('api.partidos.confirmados');
    Route::get('/partidos/pendientes', [PartidoController::class, 'pendientes'])->name('api.partidos.pendientes');
    Route::get('/partidos/cancelados', [PartidoController::class, 'cancelados'])->name('api.partidos.cancelados');
    Route::get('/partidos/hoy', [PartidoController::class, 'hoy'])->name('api.partidos.hoy');
    Route::get('/partidos/esta-semana', [PartidoController::class, 'estaSemana'])->name('api.partidos.esta-semana');
    Route::get('/partidos/futuros', [PartidoController::class, 'futuros'])->name('api.partidos.futuros');
    Route::get('/partidos/estadisticas', [PartidoController::class, 'estadisticas'])->name('api.partidos.estadisticas');
    Route::get('/partidos/calendario', [PartidoController::class, 'calendario'])->name('api.partidos.calendario');
    Route::get('/partidos/verificar-disponibilidad', [PartidoController::class, 'verificarDisponibilidad'])->name('api.partidos.verificar-disponibilidad');
    
    // Torneos
    Route::get('/torneos/por-fecha', [TorneoController::class, 'porFecha'])->name('api.torneos.por-fecha');
    Route::get('/torneos/por-equipo', [TorneoController::class, 'porEquipo'])->name('api.torneos.por-equipo');
    Route::get('/torneos/por-cancha', [TorneoController::class, 'porCancha'])->name('api.torneos.por-cancha');
    Route::get('/torneos/futuros', [TorneoController::class, 'futuros'])->name('api.torneos.futuros');
    Route::get('/torneos/pasados', [TorneoController::class, 'pasados'])->name('api.torneos.pasados');
    Route::get('/torneos/hoy', [TorneoController::class, 'hoy'])->name('api.torneos.hoy');
    Route::get('/torneos/esta-semana', [TorneoController::class, 'estaSemana'])->name('api.torneos.esta-semana');
    Route::get('/torneos/este-mes', [TorneoController::class, 'esteMes'])->name('api.torneos.este-mes');
    Route::get('/torneos/estadisticas', [TorneoController::class, 'estadisticas'])->name('api.torneos.estadisticas');
    Route::get('/torneos/calendario', [TorneoController::class, 'calendario'])->name('api.torneos.calendario');
    Route::get('/torneos/enfrentamientos', [TorneoController::class, 'enfrentamientos'])->name('api.torneos.enfrentamientos');
    Route::get('/torneos/verificar-disponibilidad', [TorneoController::class, 'verificarDisponibilidad'])->name('api.torneos.verificar-disponibilidad');
    Route::get('/torneos/tabla-posiciones', [TorneoController::class, 'tablaPosiciones'])->name('api.torneos.tabla-posiciones');
    
    // Canchas
    Route::get('/canchas/disponibles', [CanchaController::class, 'disponibles'])->name('api.canchas.disponibles');
    Route::get('/canchas/ocupadas', [CanchaController::class, 'ocupadas'])->name('api.canchas.ocupadas');
    Route::get('/canchas/activas', [CanchaController::class, 'activas'])->name('api.canchas.activas');
    Route::get('/canchas/inactivas', [CanchaController::class, 'inactivas'])->name('api.canchas.inactivas');
    Route::get('/canchas/{cancha}/horarios', [CanchaController::class, 'horarios'])->name('api.canchas.horarios');
    Route::get('/canchas/{cancha}/disponibilidad-por-fecha', [CanchaController::class, 'disponibilidadPorFecha'])->name('api.canchas.disponibilidad-por-fecha');
    Route::get('/canchas/estadisticas', [CanchaController::class, 'estadisticas'])->name('api.canchas.estadisticas');
    Route::get('/canchas/uso-por-periodo', [CanchaController::class, 'usoPorPeriodo'])->name('api.canchas.uso-por-periodo');
    Route::get('/canchas/{cancha}/calendario', [CanchaController::class, 'calendario'])->name('api.canchas.calendario');
});
