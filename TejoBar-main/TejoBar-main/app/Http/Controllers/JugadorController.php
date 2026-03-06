<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use App\Models\Persona;
use App\Models\Equipo;
use Illuminate\Http\Request;

class JugadorController extends Controller
{
    /**
     * Mostrar una lista de jugadores
     */
    public function index()
    {
        $jugadores = Jugador::with('persona')->get();
        return view('jugadores.index', compact('jugadores'));
    }

    /**
     * Mostrar el formulario para crear un nuevo jugador
     */
    public function create()
    {
        $personas = Persona::where('rol', 'jugador')->orWhere('rol', 'capitan')->get();
        return view('jugadores.create', compact('personas'));
    }

    /**
     * Almacenar un nuevo jugador
     */
    public function store(Request $request)
    {
        $request->validate([
            'idPersona' => 'required|exists:persona,idPersona|unique:jugador,idPersona',
            'rut' => 'required|string|max:50',
            'estado' => 'boolean'
        ]);

        Jugador::create([
            'idPersona' => $request->idPersona,
            'rut' => $request->rut,
            'estado' => $request->boolean('estado', true)
        ]);

        return redirect()->route('jugadores.index')
                        ->with('success', '✅ Jugador registrado correctamente');
    }

    /**
     * Mostrar un jugador específico
     */
    public function show(Jugador $jugador)
    {
        $jugador->load(['persona', 'equipos', 'compras']);
        return view('jugadores.show', compact('jugador'));
    }

    /**
     * Mostrar el formulario para editar un jugador
     */
    public function edit(Jugador $jugador)
    {
        $jugador->load('persona');
        return view('jugadores.edit', compact('jugador'));
    }

    /**
     * Actualizar un jugador
     */
    public function update(Request $request, Jugador $jugador)
    {
        $request->validate([
            'rut' => 'required|string|max:50',
            'estado' => 'boolean'
        ]);

        $jugador->update([
            'rut' => $request->rut,
            'estado' => $request->boolean('estado', true)
        ]);

        return redirect()->route('jugadores.show', $jugador)
                        ->with('success', '✅ Jugador actualizado correctamente');
    }

    /**
     * Eliminar un jugador
     */
    public function destroy(Jugador $jugador)
    {
        $jugador->delete();
        return redirect()->route('jugadores.index')
                        ->with('success', '✅ Jugador eliminado correctamente');
    }

    /**
     * Asignar jugador a un equipo
     */
    public function asignarEquipo(Request $request, Jugador $jugador)
    {
        $request->validate([
            'idEquipo' => 'required|exists:equipo,idEquipo',
            'esCapitan' => 'boolean'
        ]);

        // Verificamos si ya está en el equipo
        if ($jugador->equipos()->where('idEquipo', $request->idEquipo)->exists()) {
            return back()->with('error', 'El jugador ya está en este equipo');
        }

        $jugador->equipos()->attach($request->idEquipo, [
            'esCapitan' => $request->boolean('esCapitan', false)
        ]);

        return back()->with('success', '✅ Jugador asignado al equipo correctamente');
    }

    /**
     * Remover jugador de un equipo
     */
    public function removerEquipo(Request $request, Jugador $jugador)
    {
        $request->validate([
            'idEquipo' => 'required|exists:equipo,idEquipo'
        ]);

        $jugador->equipos()->detach($request->idEquipo);

        return back()->with('success', '✅ Jugador removido del equipo correctamente');
    }

    /**
     * Obtener jugadores activos
     */
    public function activos()
    {
        $jugadores = Jugador::where('estado', true)
                           ->with('persona')
                           ->get();
        return response()->json($jugadores);
    }

    /**
     * Obtener jugadores inactivos
     */
    public function inactivos()
    {
        $jugadores = Jugador::where('estado', false)
                           ->with('persona')
                           ->get();
        return response()->json($jugadores);
    }

    /**
     * Obtener jugadores sin equipo
     */
    public function sinEquipo()
    {
        $jugadores = Jugador::whereDoesntHave('equipos')
                           ->with('persona')
                           ->get();
        return response()->json($jugadores);
    }

    /**
     * Obtener capitanes
     */
    public function capitanes()
    {
        $capitanes = Jugador::whereHas('equipos', function ($query) {
            $query->where('esCapitan', true);
        })->with(['persona', 'equipos'])->get();

        return response()->json($capitanes);
    }

    /**
     * Obtener jugadores por equipo
     */
    public function porEquipo($idEquipo)
    {
        $jugadores = Jugador::whereHas('equipos', function ($query) use ($idEquipo) {
            $query->where('idEquipo', $idEquipo);
        })->with('persona')->get();

        return response()->json($jugadores);
    }

    /**
     * Buscar jugadores por nombre
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $jugadores = Jugador::whereHas('persona', function ($q) use ($query) {
            $q->where('nombre', 'like', "%{$query}%");
        })->with('persona')->get();

        return response()->json($jugadores);
    }

    /**
     * Obtener estadísticas de jugadores
     */
    public function estadisticas()
    {
        $estadisticas = [
            'total_jugadores' => Jugador::count(),
            'jugadores_activos' => Jugador::where('estado', true)->count(),
            'jugadores_inactivos' => Jugador::where('estado', false)->count(),
            'jugadores_sin_equipo' => Jugador::whereDoesntHave('equipos')->count(),
            'total_capitanes' => Jugador::whereHas('equipos', function ($query) {
                $query->where('esCapitan', true);
            })->count(),
            'jugadores_con_compras' => Jugador::whereHas('compras')->count(),
            'total_compras_jugadores' => Jugador::withCount('compras')->get()->sum('compras_count')
        ];

        return response()->json($estadisticas);
    }

    /**
     * Activar/desactivar jugador
     */
    public function toggleEstado(Jugador $jugador)
    {
        $jugador->update(['estado' => !$jugador->estado]);
        
        $estado = $jugador->estado ? 'activado' : 'desactivado';
        return back()->with('success', "✅ Jugador {$estado} correctamente");
    }
}
