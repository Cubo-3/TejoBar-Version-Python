<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Jugador;
use App\Models\Apartado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    /**
     * Mostrar una lista de compras
     */
    public function index()
    {
        $compras = Compra::with('jugador.persona')->orderBy('fecha', 'desc')->get();
        return view('compras.index', compact('compras'));
    }

    /**
     * Mostrar compras del usuario actual
     */
    public function misCompras()
    {
        $idPersona = Session::get('idPersona');
        
        if (!$idPersona) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        $compras = Compra::where('idJugador', $idPersona)
                        ->with('jugador.persona')
                        ->orderBy('fecha', 'desc')
                        ->get();

        return view('compras.mis-compras', compact('compras'));
    }

    /**
     * Mostrar el formulario para crear una nueva compra
     */
    public function create()
    {
        $jugadores = Jugador::with('persona')->get();
        return view('compras.create', compact('jugadores'));
    }

    /**
     * Almacenar una nueva compra
     */
    public function store(Request $request)
    {
        $request->validate([
            'idJugador' => 'required|exists:jugador,idPersona',
            'total' => 'required|numeric|min:0'
        ]);

        $compra = Compra::create([
            'idJugador' => $request->idJugador,
            'total' => $request->total,
            'fecha' => now()->toDateString()
        ]);

        return redirect()->route('compras.show', $compra)
                        ->with('success', '✅ Compra registrada correctamente');
    }

    /**
     * Mostrar una compra específica
     */
    public function show(Compra $compra)
    {
        $compra->load('jugador.persona');
        return view('compras.show', compact('compra'));
    }

    /**
     * Mostrar el formulario para editar una compra
     */
    public function edit(Compra $compra)
    {
        $jugadores = Jugador::with('persona')->get();
        return view('compras.edit', compact('compra', 'jugadores'));
    }

    /**
     * Actualizar una compra
     */
    public function update(Request $request, Compra $compra)
    {
        $request->validate([
            'idJugador' => 'required|exists:jugador,idPersona',
            'total' => 'required|numeric|min:0',
            'fecha' => 'required|date'
        ]);

        $compra->update($request->only(['idJugador', 'total', 'fecha']));

        return redirect()->route('compras.show', $compra)
                        ->with('success', '✅ Compra actualizada correctamente');
    }

    /**
     * Eliminar una compra
     */
    public function destroy(Compra $compra)
    {
        $compra->delete();
        return redirect()->route('compras.index')
                        ->with('success', '✅ Compra eliminada correctamente');
    }

    /**
     * Procesar compra desde apartado
     */
    public function procesarDesdeApartado(Request $request)
    {
        $request->validate([
            'apartado_id' => 'required|exists:apartados,idApartado'
        ]);

        $apartado = Apartado::find($request->apartado_id);
        
        if ($apartado->estado !== 'pendiente') {
            return back()->with('error', 'Este apartado ya fue procesado');
        }

        DB::transaction(function () use ($apartado) {
            // Crear la compra
            $compra = Compra::create([
                'idJugador' => $apartado->idPersona,
                'total' => $apartado->producto->precio * $apartado->cantidad,
                'fecha' => now()->toDateString()
            ]);

            // Actualizar el apartado
            $apartado->update(['estado' => 'comprado']);

            // Descontar stock
            $apartado->producto->decrement('stock', $apartado->cantidad);
        });

        return redirect()->route('compras.index')
                        ->with('success', '✅ Compra procesada desde apartado');
    }

    /**
     * Obtener compras por fecha
     */
    public function porFecha(Request $request)
    {
        $fecha = $request->input('fecha');
        $compras = Compra::porFecha($fecha)->with('jugador.persona')->get();
        return response()->json($compras);
    }

    /**
     * Obtener compras por rango de fechas
     */
    public function porRangoFechas(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        
        $compras = Compra::porRangoFechas($fechaInicio, $fechaFin)
                        ->with('jugador.persona')
                        ->get();
        return response()->json($compras);
    }

    /**
     * Obtener compras con total mayor a un valor
     */
    public function conTotalMayorA(Request $request)
    {
        $total = $request->input('total');
        $compras = Compra::conTotalMayorA($total)->with('jugador.persona')->get();
        return response()->json($compras);
    }

    /**
     * Obtener estadísticas de compras
     */
    public function estadisticas()
    {
        $estadisticas = [
            'total_compras' => Compra::count(),
            'total_ventas' => Compra::sum('total'),
            'promedio_compra' => Compra::avg('total'),
            'compras_hoy' => Compra::whereDate('fecha', today())->count(),
            'ventas_hoy' => Compra::whereDate('fecha', today())->sum('total'),
            'compras_esta_semana' => Compra::whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'ventas_esta_semana' => Compra::whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])->sum('total'),
            'compras_este_mes' => Compra::whereMonth('fecha', now()->month)->count(),
            'ventas_este_mes' => Compra::whereMonth('fecha', now()->month)->sum('total')
        ];

        return response()->json($estadisticas);
    }

    /**
     * Obtener reporte de ventas por período
     */
    public function reporteVentas(Request $request)
    {
        $periodo = $request->input('periodo', 'mes'); // dia, semana, mes, año
        
        $query = Compra::query();
        
        switch ($periodo) {
            case 'dia':
                $query->whereDate('fecha', today());
                break;
            case 'semana':
                $query->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'mes':
                $query->whereMonth('fecha', now()->month);
                break;
            case 'año':
                $query->whereYear('fecha', now()->year);
                break;
        }

        $reporte = [
            'periodo' => $periodo,
            'total_compras' => $query->count(),
            'total_ventas' => $query->sum('total'),
            'promedio_compra' => $query->avg('total'),
            'compras' => $query->with('jugador.persona')->get()
        ];

        return response()->json($reporte);
    }
}
