<?php

namespace App\Http\Controllers;

use App\Models\Cancha;
use App\Models\Partido;
use App\Models\Torneo;
use Illuminate\Http\Request;

class CanchaController extends Controller
{
    /**
     * Mostrar una lista de canchas
     */
    public function index()
    {
        $canchas = Cancha::withCount(['partidos', 'torneos'])->get();
        return view('canchas.index', compact('canchas'));
    }

    /**
     * Mostrar el formulario para crear una nueva cancha
     */
    public function create()
    {
        return view('canchas.create');
    }

    /**
     * Almacenar una nueva cancha
     */
    public function store(Request $request)
    {
        $request->validate([
            'estado' => 'boolean',
            'disponibilidad' => 'required|string|max:100'
        ]);

        Cancha::create([
            'estado' => $request->boolean('estado', true),
            'disponibilidad' => $request->disponibilidad
        ]);

        return redirect()->route('canchas.index')
                        ->with('success', '✅ Cancha creada correctamente');
    }

    /**
     * Mostrar una cancha específica
     */
    public function show(Cancha $cancha)
    {
        $cancha->load(['partidos', 'torneos']);
        
        // Obtenemos los próximos eventos
        $proximosPartidos = $cancha->partidos()
                                  ->where('fecha', '>=', today())
                                  ->where('estado', '!=', 'Cancelada')
                                  ->orderBy('fecha')
                                  ->orderBy('hora')
                                  ->take(5)
                                  ->get();

        $proximosTorneos = $cancha->torneos()
                                 ->where('fecha', '>=', today())
                                 ->orderBy('fecha')
                                 ->take(5)
                                 ->get();

        return view('canchas.show', compact('cancha', 'proximosPartidos', 'proximosTorneos'));
    }

    /**
     * Mostrar el formulario para editar una cancha
     */
    public function edit(Cancha $cancha)
    {
        return view('canchas.edit', compact('cancha'));
    }

    /**
     * Actualizar una cancha
     */
    public function update(Request $request, Cancha $cancha)
    {
        $request->validate([
            'estado' => 'boolean',
            'disponibilidad' => 'required|string|max:100'
        ]);

        $cancha->update([
            'estado' => $request->boolean('estado', true),
            'disponibilidad' => $request->disponibilidad
        ]);

        return redirect()->route('canchas.show', $cancha)
                        ->with('success', '✅ Cancha actualizada correctamente');
    }

    /**
     * Eliminar una cancha
     */
    public function destroy(Cancha $cancha)
    {
        // Verificamos si la cancha tiene partidos o torneos
        if ($cancha->partidos()->count() > 0 || $cancha->torneos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una cancha que tiene partidos o torneos programados');
        }

        $cancha->delete();
        return redirect()->route('canchas.index')
                        ->with('success', '✅ Cancha eliminada correctamente');
    }

    /**
     * Obtener canchas disponibles
     */
    public function disponibles()
    {
        $canchas = Cancha::disponibles()->get();
        return response()->json($canchas);
    }

    /**
     * Obtener canchas ocupadas
     */
    public function ocupadas()
    {
        $canchas = Cancha::ocupadas()->get();
        return response()->json($canchas);
    }

    /**
     * Obtener canchas activas
     */
    public function activas()
    {
        $canchas = Cancha::where('estado', true)->get();
        return response()->json($canchas);
    }

    /**
     * Obtener canchas inactivas
     */
    public function inactivas()
    {
        $canchas = Cancha::where('estado', false)->get();
        return response()->json($canchas);
    }

    /**
     * Obtener horarios de una cancha
     */
    public function horarios(Cancha $cancha, Request $request)
    {
        $fecha = $request->input('fecha', today()->toDateString());
        
        $partidos = $cancha->partidos()
                          ->whereDate('fecha', $fecha)
                          ->where('estado', '!=', 'Cancelada')
                          ->orderBy('hora')
                          ->get();

        $torneos = $cancha->torneos()
                         ->whereDate('fecha', $fecha)
                         ->orderBy('fecha')
                         ->get();

        return response()->json([
            'fecha' => $fecha,
            'partidos' => $partidos,
            'torneos' => $torneos
        ]);
    }

    /**
     * Obtener disponibilidad de cancha por fecha
     */
    public function disponibilidadPorFecha(Cancha $cancha, Request $request)
    {
        $fecha = $request->input('fecha', today()->toDateString());
        
        $ocupada = $cancha->partidos()
                         ->whereDate('fecha', $fecha)
                         ->where('estado', '!=', 'Cancelada')
                         ->exists() ||
                  $cancha->torneos()
                         ->whereDate('fecha', $fecha)
                         ->exists();

        return response()->json([
            'cancha_id' => $cancha->idCancha,
            'fecha' => $fecha,
            'disponible' => !$ocupada,
            'ocupada' => $ocupada,
            'estado' => $cancha->estado,
            'disponibilidad' => $cancha->disponibilidad
        ]);
    }

    /**
     * Cambiar estado de cancha
     */
    public function cambiarEstado(Cancha $cancha)
    {
        $cancha->update(['estado' => !$cancha->estado]);
        
        $estado = $cancha->estado ? 'activada' : 'desactivada';
        return back()->with('success', "✅ Cancha {$estado} correctamente");
    }

    /**
     * Cambiar disponibilidad de cancha
     */
    public function cambiarDisponibilidad(Request $request, Cancha $cancha)
    {
        $request->validate([
            'disponibilidad' => 'required|string|max:100'
        ]);

        $cancha->update(['disponibilidad' => $request->disponibilidad]);
        
        return back()->with('success', '✅ Disponibilidad actualizada correctamente');
    }

    /**
     * Obtener estadísticas de canchas
     */
    public function estadisticas()
    {
        $estadisticas = [
            'total_canchas' => Cancha::count(),
            'canchas_disponibles' => Cancha::disponibles()->count(),
            'canchas_ocupadas' => Cancha::ocupadas()->count(),
            'canchas_activas' => Cancha::where('estado', true)->count(),
            'canchas_inactivas' => Cancha::where('estado', false)->count(),
            'total_partidos' => Partido::count(),
            'total_torneos' => Torneo::count(),
            'partidos_hoy' => Partido::whereDate('fecha', today())->count(),
            'torneos_hoy' => Torneo::whereDate('fecha', today())->count()
        ];

        return response()->json($estadisticas);
    }

    /**
     * Obtener uso de canchas por período
     */
    public function usoPorPeriodo(Request $request)
    {
        $periodo = $request->input('periodo', 'mes'); // dia, semana, mes, año
        
        $queryPartidos = Partido::query();
        $queryTorneos = Torneo::query();
        
        switch ($periodo) {
            case 'dia':
                $queryPartidos->whereDate('fecha', today());
                $queryTorneos->whereDate('fecha', today());
                break;
            case 'semana':
                $queryPartidos->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]);
                $queryTorneos->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'mes':
                $queryPartidos->whereMonth('fecha', now()->month);
                $queryTorneos->whereMonth('fecha', now()->month);
                break;
            case 'año':
                $queryPartidos->whereYear('fecha', now()->year);
                $queryTorneos->whereYear('fecha', now()->year);
                break;
        }

        $uso = Cancha::withCount([
            'partidos' => function ($query) use ($queryPartidos) {
                $query->whereIn('idPartido', $queryPartidos->pluck('idPartido'));
            },
            'torneos' => function ($query) use ($queryTorneos) {
                $query->whereIn('idPartido', $queryTorneos->pluck('idPartido'));
            }
        ])->get();

        return response()->json($uso);
    }

    /**
     * Obtener calendario de cancha
     */
    public function calendario(Cancha $cancha, Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $año = $request->input('año', now()->year);

        $partidos = $cancha->partidos()
                          ->whereMonth('fecha', $mes)
                          ->whereYear('fecha', $año)
                          ->orderBy('fecha')
                          ->orderBy('hora')
                          ->get();

        $torneos = $cancha->torneos()
                         ->whereMonth('fecha', $mes)
                         ->whereYear('fecha', $año)
                         ->orderBy('fecha')
                         ->get();

        return response()->json([
            'cancha' => $cancha,
            'mes' => $mes,
            'año' => $año,
            'partidos' => $partidos,
            'torneos' => $torneos
        ]);
    }
}
