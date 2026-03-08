<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Equipo;
use App\Models\Cancha;
use Illuminate\Http\Request;

class TorneoController extends Controller
{
    /**
     * Mostrar una lista de torneos
     */
    public function index()
    {
        $torneos = Torneo::with(['equipo1', 'equipo2', 'cancha'])->orderBy('fecha', 'desc')->get();
        return view('torneos.index', compact('torneos'));
    }

    /**
     * Mostrar el formulario para crear un nuevo torneo
     */
    public function create()
    {
        $equipos = Equipo::all();
        $canchas = Cancha::disponibles()->get();
        return view('torneos.create', compact('equipos', 'canchas'));
    }

    /**
     * Almacenar un nuevo torneo
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'equipo1' => 'required|exists:equipo,idEquipo',
            'equipo2' => 'required|exists:equipo,idEquipo|different:equipo1',
            'cancha' => 'required|exists:cancha,idCancha'
        ]);

        // Verificamos que los equipos sean diferentes
        if ($request->equipo1 === $request->equipo2) {
            return back()->with('error', 'Los equipos deben ser diferentes');
        }

        // Verificamos que la cancha esté disponible
        $canchaOcupada = Torneo::where('cancha', $request->cancha)
                              ->whereDate('fecha', $request->fecha)
                              ->exists();

        if ($canchaOcupada) {
            return back()->with('error', 'La cancha ya está ocupada en esa fecha');
        }

        Torneo::create([
            'fecha' => $request->fecha,
            'equipo1' => $request->equipo1,
            'equipo2' => $request->equipo2,
            'cancha' => $request->cancha
        ]);

        return redirect()->route('torneos.index')
                        ->with('success', '✅ Torneo programado correctamente');
    }

    /**
     * Mostrar un torneo específico
     */
    public function show(Torneo $torneo)
    {
        $torneo->load(['equipo1', 'equipo2', 'cancha']);
        return view('torneos.show', compact('torneo'));
    }

    /**
     * Mostrar el formulario para editar un torneo
     */
    public function edit(Torneo $torneo)
    {
        $equipos = Equipo::all();
        $canchas = Cancha::all();
        return view('torneos.edit', compact('torneo', 'equipos', 'canchas'));
    }

    /**
     * Actualizar un torneo
     */
    public function update(Request $request, Torneo $torneo)
    {
        $request->validate([
            'fecha' => 'required|date',
            'equipo1' => 'required|exists:equipo,idEquipo',
            'equipo2' => 'required|exists:equipo,idEquipo|different:equipo1',
            'cancha' => 'required|exists:cancha,idCancha'
        ]);

        // Verificamos que los equipos sean diferentes
        if ($request->equipo1 === $request->equipo2) {
            return back()->with('error', 'Los equipos deben ser diferentes');
        }

        // Verificamos que la cancha esté disponible (excluyendo el torneo actual)
        $canchaOcupada = Torneo::where('cancha', $request->cancha)
                              ->whereDate('fecha', $request->fecha)
                              ->where('idPartido', '!=', $torneo->idPartido)
                              ->exists();

        if ($canchaOcupada) {
            return back()->with('error', 'La cancha ya está ocupada en esa fecha');
        }

        $torneo->update($request->only(['fecha', 'equipo1', 'equipo2', 'cancha']));

        return redirect()->route('torneos.show', $torneo)
                        ->with('success', '✅ Torneo actualizado correctamente');
    }

    /**
     * Eliminar un torneo
     */
    public function destroy(Torneo $torneo)
    {
        $torneo->delete();
        return redirect()->route('torneos.index')
                        ->with('success', '✅ Torneo eliminado correctamente');
    }

    /**
     * Obtener torneos por fecha
     */
    public function porFecha(Request $request)
    {
        $fecha = $request->input('fecha');
        $torneos = Torneo::porFecha($fecha)->with(['equipo1', 'equipo2', 'cancha'])->get();
        return response()->json($torneos);
    }

    /**
     * Obtener torneos por equipo
     */
    public function porEquipo(Request $request)
    {
        $equipoId = $request->input('equipo_id');
        $torneos = Torneo::porEquipo($equipoId)->with(['equipo1', 'equipo2', 'cancha'])->get();
        return response()->json($torneos);
    }

    /**
     * Obtener torneos por cancha
     */
    public function porCancha(Request $request)
    {
        $canchaId = $request->input('cancha_id');
        $torneos = Torneo::porCancha($canchaId)->with(['equipo1', 'equipo2', 'cancha'])->get();
        return response()->json($torneos);
    }

    /**
     * Obtener torneos futuros
     */
    public function futuros()
    {
        $torneos = Torneo::futuros()->with(['equipo1', 'equipo2', 'cancha'])->get();
        return response()->json($torneos);
    }

    /**
     * Obtener torneos pasados
     */
    public function pasados()
    {
        $torneos = Torneo::pasados()->with(['equipo1', 'equipo2', 'cancha'])->get();
        return response()->json($torneos);
    }

    /**
     * Obtener torneos de hoy
     */
    public function hoy()
    {
        $torneos = Torneo::whereDate('fecha', today())
                        ->with(['equipo1', 'equipo2', 'cancha'])
                        ->orderBy('fecha')
                        ->get();
        return response()->json($torneos);
    }

    /**
     * Obtener torneos de esta semana
     */
    public function estaSemana()
    {
        $torneos = Torneo::whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])
                        ->with(['equipo1', 'equipo2', 'cancha'])
                        ->orderBy('fecha')
                        ->get();
        return response()->json($torneos);
    }

    /**
     * Obtener torneos de este mes
     */
    public function esteMes()
    {
        $torneos = Torneo::whereMonth('fecha', now()->month)
                        ->whereYear('fecha', now()->year)
                        ->with(['equipo1', 'equipo2', 'cancha'])
                        ->orderBy('fecha')
                        ->get();
        return response()->json($torneos);
    }

    /**
     * Obtener estadísticas de torneos
     */
    public function estadisticas()
    {
        $estadisticas = [
            'total_torneos' => Torneo::count(),
            'torneos_futuros' => Torneo::futuros()->count(),
            'torneos_pasados' => Torneo::pasados()->count(),
            'torneos_hoy' => Torneo::whereDate('fecha', today())->count(),
            'torneos_esta_semana' => Torneo::whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'torneos_este_mes' => Torneo::whereMonth('fecha', now()->month)->count(),
            'equipos_participantes' => Torneo::selectRaw('COUNT(DISTINCT equipo1) + COUNT(DISTINCT equipo2) as total')
                                                ->first()->total ?? 0
        ];

        return response()->json($estadisticas);
    }

    /**
     * Obtener calendario de torneos
     */
    public function calendario(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $año = $request->input('año', now()->year);

        $torneos = Torneo::whereMonth('fecha', $mes)
                        ->whereYear('fecha', $año)
                        ->with(['equipo1', 'equipo2', 'cancha'])
                        ->orderBy('fecha')
                        ->get();

        return response()->json($torneos);
    }

    /**
     * Obtener enfrentamientos entre equipos
     */
    public function enfrentamientos(Request $request)
    {
        $equipo1 = $request->input('equipo1');
        $equipo2 = $request->input('equipo2');

        $torneos = Torneo::where(function ($query) use ($equipo1, $equipo2) {
            $query->where('equipo1', $equipo1)->where('equipo2', $equipo2)
                  ->orWhere('equipo1', $equipo2)->where('equipo2', $equipo1);
        })->with(['equipo1', 'equipo2', 'cancha'])->get();

        return response()->json($torneos);
    }

    /**
     * Verificar disponibilidad de cancha para torneo
     */
    public function verificarDisponibilidad(Request $request)
    {
        $request->validate([
            'cancha_id' => 'required|exists:cancha,idCancha',
            'fecha' => 'required|date',
            'torneo_id' => 'nullable|exists:torneo,idPartido'
        ]);

        $query = Torneo::where('cancha', $request->cancha_id)
                      ->whereDate('fecha', $request->fecha);

        if ($request->torneo_id) {
            $query->where('idPartido', '!=', $request->torneo_id);
        }

        $ocupado = $query->exists();

        return response()->json([
            'disponible' => !$ocupado,
            'ocupado' => $ocupado
        ]);
    }

    /**
     * Obtener tabla de posiciones (simulada)
     */
    public function tablaPosiciones()
    {
        $equipos = Equipo::withCount(['torneosComoEquipo1', 'torneosComoEquipo2'])->get();
        
        $tabla = $equipos->map(function ($equipo) {
            return [
                'equipo' => $equipo->nombreEquipo,
                'partidos_jugados' => $equipo->torneos_como_equipo1_count + $equipo->torneos_como_equipo2_count,
                'victorias' => 0, // Aquí se puede agregar lógica de resultados después
                'empates' => 0,
                'derrotas' => 0,
                'puntos' => 0
            ];
        })->sortByDesc('puntos');

        return response()->json($tabla);
    }
}
