<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Cancha;
use Illuminate\Http\Request;

class PartidoController extends Controller
{
    /**
     * Mostrar una lista de partidos
     */
    public function index()
    {
        $partidos = Partido::with('cancha')->orderBy('fecha', 'desc')->get();
        return view('partidos.index', compact('partidos'));
    }

    /**
     * Mostrar el formulario para crear un nuevo partido
     */
    public function create()
    {
        $canchas = Cancha::disponibles()->get();
        return view('partidos.create', compact('canchas'));
    }

    /**
     * Almacenar un nuevo partido
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|string|max:20',
            'capitan' => 'required|string|max:100',
            'cancha' => 'required|exists:cancha,idCancha',
            'estado' => 'in:Pendiente,Confirmada,Cancelada'
        ]);

        // Verificamos que la cancha esté disponible
        $canchaOcupada = Partido::where('cancha', $request->cancha)
                               ->where('fecha', $request->fecha)
                               ->where('estado', '!=', 'Cancelada')
                               ->exists();

        if ($canchaOcupada) {
            return back()->with('error', 'La cancha ya está ocupada en esa fecha');
        }

        Partido::create([
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'capitan' => $request->capitan,
            'cancha' => $request->cancha,
            'estado' => $request->estado ?? 'Pendiente'
        ]);

        return redirect()->route('partidos.index')
                        ->with('success', '✅ Partido programado correctamente');
    }

    /**
     * Mostrar un partido específico
     */
    public function show(Partido $partido)
    {
        $partido->load('cancha');
        return view('partidos.show', compact('partido'));
    }

    /**
     * Mostrar el formulario para editar un partido
     */
    public function edit(Partido $partido)
    {
        $canchas = Cancha::all();
        return view('partidos.edit', compact('partido', 'canchas'));
    }

    /**
     * Actualizar un partido
     */
    public function update(Request $request, Partido $partido)
    {
        $request->validate([
            'fecha' => 'required|date',
            'hora' => 'required|string|max:20',
            'capitan' => 'required|string|max:100',
            'cancha' => 'required|exists:cancha,idCancha',
            'estado' => 'required|in:Pendiente,Confirmada,Cancelada'
        ]);

        // Verificamos que la cancha esté disponible (excluyendo el partido actual)
        $canchaOcupada = Partido::where('cancha', $request->cancha)
                               ->where('fecha', $request->fecha)
                               ->where('idPartido', '!=', $partido->idPartido)
                               ->where('estado', '!=', 'Cancelada')
                               ->exists();

        if ($canchaOcupada) {
            return back()->with('error', 'La cancha ya está ocupada en esa fecha');
        }

        $partido->update($request->only(['fecha', 'hora', 'capitan', 'cancha', 'estado']));

        return redirect()->route('partidos.show', $partido)
                        ->with('success', '✅ Partido actualizado correctamente');
    }

    /**
     * Eliminar un partido
     */
    public function destroy(Partido $partido)
    {
        $partido->delete();
        return redirect()->route('partidos.index')
                        ->with('success', '✅ Partido eliminado correctamente');
    }

    /**
     * Confirmar un partido
     */
    public function confirmar(Partido $partido)
    {
        $partido->update(['estado' => 'Confirmada']);
        return back()->with('success', '✅ Partido confirmado correctamente');
    }

    /**
     * Cancelar un partido (solo para capitanes)
     */
    public function cancelar(Request $request, Partido $partido)
    {
        $capitanId = session('idPersona');
        
        if (!$capitanId) {
            return back()->with('error', 'Debes estar logueado para cancelar partidos');
        }

        $capitan = \App\Models\Persona::find($capitanId);
        
        if (!$capitan) {
            return back()->with('error', 'Usuario no encontrado');
        }

        // Verificamos si el partido pertenece al capitán
        if ($partido->capitan !== $capitan->nombre) {
            return back()->with('error', 'Solo puedes cancelar tus propios partidos');
        }

        // Verificamos si el partido ya está cancelado
        if ($partido->estado === 'Cancelada') {
            return back()->with('error', 'Este partido ya está cancelado');
        }

        // Verificamos si el partido ya pasó
        if ($partido->fecha < now()->toDateString()) {
            return back()->with('error', 'No puedes cancelar un partido que ya pasó');
        }

        // Cancelamos el partido
        $partido->update(['estado' => 'Cancelada']);

        return back()->with('success', '✅ Partido cancelado correctamente');
    }

    /**
     * Obtener partidos por fecha
     */
    public function porFecha(Request $request)
    {
        $fecha = $request->input('fecha');
        $partidos = Partido::porFecha($fecha)->with('cancha')->get();
        return response()->json($partidos);
    }

    /**
     * Obtener partidos por cancha
     */
    public function porCancha(Request $request)
    {
        $canchaId = $request->input('cancha_id');
        $partidos = Partido::porCancha($canchaId)->with('cancha')->get();
        return response()->json($partidos);
    }

    /**
     * Obtener partidos confirmados
     */
    public function confirmados()
    {
        $partidos = Partido::confirmados()->with('cancha')->get();
        return response()->json($partidos);
    }

    /**
     * Obtener partidos pendientes
     */
    public function pendientes()
    {
        $partidos = Partido::pendientes()->with('cancha')->get();
        return response()->json($partidos);
    }

    /**
     * Obtener partidos cancelados
     */
    public function cancelados()
    {
        $partidos = Partido::cancelados()->with('cancha')->get();
        return response()->json($partidos);
    }

    /**
     * Obtener partidos de hoy
     */
    public function hoy()
    {
        $partidos = Partido::whereDate('fecha', today())
                          ->with('cancha')
                          ->orderBy('hora')
                          ->get();
        return response()->json($partidos);
    }

    /**
     * Obtener partidos de esta semana
     */
    public function estaSemana()
    {
        $partidos = Partido::whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])
                          ->with('cancha')
                          ->orderBy('fecha')
                          ->orderBy('hora')
                          ->get();
        return response()->json($partidos);
    }

    /**
     * Obtener partidos futuros
     */
    public function futuros()
    {
        $partidos = Partido::where('fecha', '>=', today())
                          ->where('estado', '!=', 'Cancelada')
                          ->with('cancha')
                          ->orderBy('fecha')
                          ->orderBy('hora')
                          ->get();
        return response()->json($partidos);
    }

    /**
     * Obtener estadísticas de partidos
     */
    public function estadisticas()
    {
        $estadisticas = [
            'total_partidos' => Partido::count(),
            'partidos_confirmados' => Partido::confirmados()->count(),
            'partidos_pendientes' => Partido::pendientes()->count(),
            'partidos_cancelados' => Partido::cancelados()->count(),
            'partidos_hoy' => Partido::whereDate('fecha', today())->count(),
            'partidos_esta_semana' => Partido::whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'partidos_este_mes' => Partido::whereMonth('fecha', now()->month)->count(),
            'partidos_futuros' => Partido::where('fecha', '>=', today())->count()
        ];

        return response()->json($estadisticas);
    }

    /**
     * Obtener calendario de partidos
     */
    public function calendario(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $año = $request->input('año', now()->year);

        $partidos = Partido::whereMonth('fecha', $mes)
                          ->whereYear('fecha', $año)
                          ->with('cancha')
                          ->orderBy('fecha')
                          ->orderBy('hora')
                          ->get();

        return response()->json($partidos);
    }

    /**
     * Verificar disponibilidad de cancha
     */
    public function verificarDisponibilidad(Request $request)
    {
        $request->validate([
            'cancha_id' => 'required|exists:cancha,idCancha',
            'fecha' => 'required|date',
            'partido_id' => 'nullable|exists:partido,idPartido'
        ]);

        $query = Partido::where('cancha', $request->cancha_id)
                       ->where('fecha', $request->fecha)
                       ->where('estado', '!=', 'Cancelada');

        if ($request->partido_id) {
            $query->where('idPartido', '!=', $request->partido_id);
        }

        $ocupado = $query->exists();

        return response()->json([
            'disponible' => !$ocupado,
            'ocupado' => $ocupado
        ]);
    }

}
