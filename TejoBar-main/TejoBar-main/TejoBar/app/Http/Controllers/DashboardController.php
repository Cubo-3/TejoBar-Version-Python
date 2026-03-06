<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Producto;
use App\Models\Apartado;
use App\Models\Compra;
use App\Models\Partido;
use App\Models\Torneo;
use App\Models\Equipo;
use App\Models\Cancha;
use App\Models\Historial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard principal
     */
    public function index()
    {
        $idPersona = Session::get('idPersona');
        $rol = Session::get('rol');

        if (!$idPersona) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        $usuario = Persona::find($idPersona);
        
        if (!$usuario) {
            Session::flush();
            return redirect()->route('login')->with('error', 'Usuario no encontrado. Por favor, inicia sesión nuevamente.');
        }

        // Sincronizar el rol de la sesión con el estado real en la base de datos
        $this->sincronizarRol($usuario);
        $rol = Session::get('rol'); // Actualizar el rol después de la sincronización

        $data = [
            'usuario' => $usuario,
            'rol' => $rol
        ];

        // Datos específicos según el rol
        switch ($rol) {
            case 'admin':
                $data = array_merge($data, $this->getAdminData());
                break;
            case 'capitan':
                $data = array_merge($data, $this->getCapitanData($idPersona));
                break;
            case 'jugador':
                $data = array_merge($data, $this->getJugadorData($idPersona));
                break;
        }

        return view('dashboard.index', $data);
    }

    /**
     * Obtener datos para administrador
     */
    private function getAdminData()
    {
        return [
            'totalProductos' => Producto::count(),
            'productosBajoStock' => Producto::where('stock', '<', 10)->count(),
            'totalApartados' => Apartado::count(),
            'apartadosPendientes' => Apartado::pendientes()->count(),
            'totalCompras' => Compra::count(),
            'totalPartidos' => Partido::count(),
            'partidosConfirmados' => Partido::confirmados()->count(),
            'totalTorneos' => Torneo::count(),
            'totalEquipos' => Equipo::count(),
            'totalCanchas' => Cancha::count(),
            'canchasDisponibles' => Cancha::disponibles()->count(),
            'productos' => Producto::with('apartados')->get(),
            'apartados' => Apartado::with(['persona', 'producto'])->orderBy('fechaApartado', 'desc')->get(),
            'compras' => Compra::with('jugador.persona')->orderBy('fecha', 'desc')->take(10)->get(),
            'partidos' => Partido::with('cancha')->orderBy('fecha', 'desc')->take(10)->get(),
            'torneos' => Torneo::with(['equipo1', 'equipo2', 'cancha'])->orderBy('fecha', 'desc')->take(10)->get()
        ];
    }

    /**
     * Obtener datos para capitán
     */
    private function getCapitanData($idPersona)
    {
        $capitan = Persona::find($idPersona);
        $jugador = $capitan->jugador;
        $equipos = $jugador ? $jugador->equipos : collect();

        // Obtener torneos/partidos del equipo del capitán
        $equipo = $equipos->first();
        if ($equipo) {
            $misPartidos = Torneo::where('equipo1', $equipo->idEquipo)
                ->orWhere('equipo2', $equipo->idEquipo)
                ->with('cancha')
                ->orderBy('fecha', 'desc')
                ->get();
        } else {
            $misPartidos = collect();
        }


        return [
            'misEquipos' => $equipos,
            'misApartados' => Apartado::where('idPersona', $idPersona)->with('producto')->get(),
            'misCompras' => Compra::where('idJugador', $idPersona)->get(),
            'partidosEquipo' => collect(), // Se puede implementar lógica específica
            'partidos' => Torneo::with('cancha')->orderBy('fecha', 'desc')->get(),
            'misPartidos' => $misPartidos,
            'productos' => Producto::disponibles()->get()
        ];
    }

    /**
     * Obtener datos para jugador
     */
    private function getJugadorData($idPersona)
    {
        return [
            'misApartados' => Apartado::where('idPersona', $idPersona)->with('producto')->get(),
            'misCompras' => Compra::where('idJugador', $idPersona)->get(),
            'productos' => Producto::disponibles()->get(),
            'partidos' => Torneo::with('cancha')->orderBy('fecha', 'desc')->get(),
            'torneos' => Torneo::with('cancha')->orderBy('fecha', 'desc')->get()
        ];
    }

    /**
     * Obtener estadísticas generales (API)
     */
    public function estadisticas()
    {
        $estadisticas = [
            'productos' => [
                'total' => Producto::count(),
                'disponibles' => Producto::disponibles()->count(),
                'bajo_stock' => Producto::where('stock', '<', 10)->count(),
                'proximos_vencer' => Producto::proximosAVencer(30)->count()
            ],
            'apartados' => [
                'total' => Apartado::count(),
                'pendientes' => Apartado::pendientes()->count(),
                'comprados' => Apartado::comprados()->count()
            ],
            'compras' => [
                'total' => Compra::count(),
                'hoy' => Compra::whereDate('fecha', today())->count(),
                'esta_semana' => Compra::whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])->count()
            ],
            'partidos' => [
                'total' => Partido::count(),
                'confirmados' => Partido::confirmados()->count(),
                'pendientes' => Partido::pendientes()->count(),
                'cancelados' => Partido::cancelados()->count()
            ],
            'torneos' => [
                'total' => Torneo::count(),
                'futuros' => Torneo::futuros()->count(),
                'pasados' => Torneo::pasados()->count()
            ],
            'equipos' => [
                'total' => Equipo::count()
            ],
            'canchas' => [
                'total' => Cancha::count(),
                'disponibles' => Cancha::disponibles()->count(),
                'ocupadas' => Cancha::ocupadas()->count()
            ]
        ];

        return response()->json($estadisticas);
    }

    /**
     * Obtener actividad reciente (API)
     */
    public function actividadReciente()
    {
        $actividad = [
            'apartados' => Apartado::with(['persona', 'producto'])
                                ->orderBy('fechaApartado', 'desc')
                                ->take(5)
                                ->get(),
            'compras' => Compra::with('jugador.persona')
                             ->orderBy('fecha', 'desc')
                             ->take(5)
                             ->get(),
            'partidos' => Partido::with('cancha')
                               ->orderBy('fecha', 'desc')
                               ->take(5)
                               ->get(),
            'torneos' => Torneo::with(['equipo1', 'equipo2'])
                             ->orderBy('fecha', 'desc')
                             ->take(5)
                             ->get()
        ];

        return response()->json($actividad);
    }

    /**
     * Mostrar la vista de productos del dashboard
     */
    public function productos()
    {
        $idPersona = Session::get('idPersona');
        $rol = Session::get('rol');

        if (!$idPersona) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        $usuario = Persona::find($idPersona);
        
        if (!$usuario) {
            Session::flush();
            return redirect()->route('login')->with('error', 'Usuario no encontrado. Por favor, inicia sesión nuevamente.');
        }

        $data = [
            'usuario' => $usuario,
            'rol' => $rol
        ];

        // Datos específicos según el rol
        switch ($rol) {
            case 'admin':
                $data['productos'] = Producto::all();
                $data['apartados'] = Apartado::with(['persona', 'producto'])->orderBy('fechaApartado', 'desc')->get();
                break;
            case 'capitan':
            case 'jugador':
                $data['apartados'] = Apartado::where('idPersona', $idPersona)
                    ->where('estado', 'pendiente')
                    ->with('producto')
                    ->get();
                break;
        }

        return view('dashboard.productos', $data);
    }

    public function historial()
    {
        $idPersona = Session::get('idPersona');
        $rol = Session::get('rol');
        $usuario = Persona::find($idPersona);

        $data = [
            'usuario' => $usuario,
            'rol' => $rol
        ];

        // Datos específicos según el rol
        if ($rol === 'admin') {
            // Admin ve todo el historial de apartados (pendientes y entregados)
            $data['apartadosPendientes'] = Apartado::with(['persona', 'producto'])
                ->where('estado', 'pendiente')
                ->orderBy('fechaApartado', 'desc')
                ->get();
            
            $data['apartadosEntregados'] = Historial::with(['persona', 'producto'])
                ->orderBy('fechaEntrega', 'desc')
                ->get();
        } else {
            // Jugador/Capitán ve solo su historial de apartados
            $data['misApartadosPendientes'] = Apartado::where('idPersona', $idPersona)
                ->where('estado', 'pendiente')
                ->with('producto')
                ->orderBy('fechaApartado', 'desc')
                ->get();
            
            $data['misApartadosEntregados'] = Historial::where('idPersona', $idPersona)
                ->with('producto')
                ->orderBy('fechaEntrega', 'desc')
                ->get();
        }

        return view('dashboard.historial', $data);
    }

    /**
     * Sincronizar el rol de la sesión con el estado real en la base de datos
     */
    private function sincronizarRol($usuario)
    {
        $rolSesion = Session::get('rol');
        $rolBaseDatos = $usuario->rol;

        // Si el usuario es jugador o capitán, verificar si realmente es capitán de algún equipo
        if (($rolSesion === 'jugador' || $rolSesion === 'capitan') && $usuario->jugador) {
            $esCapitanReal = $usuario->jugador->equipos()
                ->wherePivot('esCapitan', true)
                ->exists();

            if ($esCapitanReal && $rolSesion !== 'capitan') {
                // Actualizar a capitán en la base de datos y sesión
                $usuario->update(['rol' => 'capitan']);
                Session::put('rol', 'capitan');
            } elseif (!$esCapitanReal && $rolSesion === 'capitan') {
                // Actualizar a jugador en la base de datos y sesión
                $usuario->update(['rol' => 'jugador']);
                Session::put('rol', 'jugador');
            }
        }

        // Si el rol en la base de datos es diferente al de la sesión, sincronizar
        if ($rolSesion !== $rolBaseDatos) {
            Session::put('rol', $rolBaseDatos);
        }
    }
}
