<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Jugador;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    /**
     * Mostrar una lista de equipos
     */
    public function index()
    {
        $equipos = Equipo::withCount('jugadores')->get();
        return view('equipos.index', compact('equipos'));
    }

    /**
     * Mostrar el formulario para crear un nuevo equipo
     */
    public function create()
    {
        return view('equipos.create');
    }

    /**
     * Almacenar un nuevo equipo
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombreEquipo' => 'required|string|max:100|unique:equipo,nombreEquipo'
        ]);

        // Creamos el equipo usando consulta SQL directa
        $equipoId = \DB::table('equipo')->insertGetId([
            'nombreEquipo' => $request->nombreEquipo
        ]);

        return redirect()->route('equipos.index')
                        ->with('success', '✅ Equipo creado correctamente');
    }

    /**
     * Mostrar un equipo específico
     */
    public function show(Equipo $equipo)
    {
        $equipo->load(['jugadores.persona', 'torneosComoEquipo1', 'torneosComoEquipo2']);
        $jugadoresDisponibles = Jugador::whereDoesntHave('equipos')
                                      ->with('persona')
                                      ->get();
        
        return view('equipos.show', compact('equipo', 'jugadoresDisponibles'));
    }

    /**
     * Mostrar el formulario para editar un equipo
     */
    public function edit(Equipo $equipo)
    {
        return view('equipos.edit', compact('equipo'));
    }

    /**
     * Actualizar un equipo
     */
    public function update(Request $request, Equipo $equipo)
    {
        $request->validate([
            'nombreEquipo' => 'required|string|max:100|unique:equipo,nombreEquipo,' . $equipo->idEquipo . ',idEquipo'
        ]);

        $equipo->update([
            'nombreEquipo' => $request->nombreEquipo
        ]);

        return redirect()->route('equipos.show', $equipo)
                        ->with('success', '✅ Equipo actualizado correctamente');
    }

    /**
     * Eliminar un equipo
     */
    public function destroy(Equipo $equipo)
    {
        // Verificamos si el equipo tiene jugadores
        if ($equipo->jugadores()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un equipo que tiene jugadores asignados');
        }

        // Verificamos si el equipo tiene torneos
        if ($equipo->torneosComoEquipo1()->count() > 0 || $equipo->torneosComoEquipo2()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un equipo que tiene torneos programados');
        }

        $equipo->delete();
        return redirect()->route('equipos.index')
                        ->with('success', '✅ Equipo eliminado correctamente');
    }

    /**
     * Agregar jugador al equipo
     */
    public function agregarJugador(Request $request, Equipo $equipo)
    {
        $request->validate([
            'idJugador' => 'required|exists:jugador,idPersona',
            'esCapitan' => 'boolean'
        ]);

        $jugador = Jugador::find($request->idJugador);

        // Verificamos si ya está en el equipo
        if ($equipo->jugadores()->where('idJugador', $request->idJugador)->exists()) {
            return back()->with('error', 'El jugador ya está en este equipo');
        }

        // Si se asigna como capitán, quitamos al capitán anterior
        if ($request->boolean('esCapitan')) {
            $equipo->jugadores()->updateExistingPivot(
                $equipo->jugadores()->wherePivot('esCapitan', true)->pluck('idPersona'),
                ['esCapitan' => false]
            );
        }

        $equipo->jugadores()->attach($request->idJugador, [
            'esCapitan' => $request->boolean('esCapitan', false)
        ]);

        return back()->with('success', '✅ Jugador agregado al equipo correctamente');
    }

    /**
     * Remover jugador del equipo
     */
    public function removerJugador(Request $request, Equipo $equipo)
    {
        $request->validate([
            'idJugador' => 'required|exists:jugador,idPersona'
        ]);

        $equipo->jugadores()->detach($request->idJugador);

        return back()->with('success', '✅ Jugador removido del equipo correctamente');
    }

    /**
     * Cambiar capitán del equipo
     */
    public function cambiarCapitan(Request $request, Equipo $equipo)
    {
        $request->validate([
            'idJugador' => 'required|exists:jugador,idPersona'
        ]);

        // Verificamos que el jugador esté en el equipo
        if (!$equipo->jugadores()->where('idJugador', $request->idJugador)->exists()) {
            return back()->with('error', 'El jugador no está en este equipo');
        }

        // Quitamos al capitán actual
        $equipo->jugadores()->updateExistingPivot(
            $equipo->jugadores()->wherePivot('esCapitan', true)->pluck('idPersona'),
            ['esCapitan' => false]
        );

        // Asignamos al nuevo capitán
        $equipo->jugadores()->updateExistingPivot($request->idJugador, [
            'esCapitan' => true
        ]);

        return back()->with('success', '✅ Capitán cambiado correctamente');
    }

    /**
     * Obtener equipos con jugadores
     */
    public function conJugadores()
    {
        $equipos = Equipo::with(['jugadores.persona'])->get();
        return response()->json($equipos);
    }

    /**
     * Obtener equipos sin jugadores
     */
    public function sinJugadores()
    {
        $equipos = Equipo::whereDoesntHave('jugadores')->get();
        return response()->json($equipos);
    }

    /**
     * Obtener equipos con torneos
     */
    public function conTorneos()
    {
        $equipos = Equipo::whereHas('torneosComoEquipo1')
                        ->orWhereHas('torneosComoEquipo2')
                        ->with(['torneosComoEquipo1', 'torneosComoEquipo2'])
                        ->get();
        return response()->json($equipos);
    }

    /**
     * Buscar equipos por nombre
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $equipos = Equipo::where('nombreEquipo', 'like', "%{$query}%")->get();
        return response()->json($equipos);
    }

    /**
     * Obtener estadísticas de equipos
     */
    public function estadisticas()
    {
        $estadisticas = [
            'total_equipos' => Equipo::count(),
            'equipos_con_jugadores' => Equipo::whereHas('jugadores')->count(),
            'equipos_sin_jugadores' => Equipo::whereDoesntHave('jugadores')->count(),
            'equipos_con_torneos' => Equipo::whereHas('torneosComoEquipo1')
                                          ->orWhereHas('torneosComoEquipo2')
                                          ->count(),
            'total_jugadores_en_equipos' => Equipo::withCount('jugadores')->get()->sum('jugadores_count'),
            'promedio_jugadores_por_equipo' => Equipo::withCount('jugadores')->get()->avg('jugadores_count')
        ];

        return response()->json($estadisticas);
    }

    /**
     * Obtener historial de torneos del equipo
     */
    public function historialTorneos(Equipo $equipo)
    {
        $torneos = $equipo->torneos();
        return response()->json($torneos);
    }

    /**
     * Mostrar equipos disponibles para jugadores
     */
    public function disponibles()
    {
        $equipos = Equipo::with(['jugadores.persona'])
                        ->withCount('jugadores')
                        ->orderBy('nombreEquipo')
                        ->get();
        
        $jugadorId = session('idPersona');
        $jugadorActual = null;
        
        if ($jugadorId) {
            $jugadorActual = Jugador::with('equipos')->find($jugadorId);
        }
        
        return view('equipos.disponibles', compact('equipos', 'jugadorActual'));
    }

    /**
     * Unirse a un equipo
     */
    public function unirse(Request $request, Equipo $equipo)
    {
        $jugadorId = session('idPersona');
        
        if (!$jugadorId) {
            return back()->with('error', 'Debes estar logueado para unirte a un equipo');
        }

        $jugador = Jugador::find($jugadorId);
        
        if (!$jugador) {
            return back()->with('error', 'Jugador no encontrado');
        }

        // Verificamos si ya está en algún equipo
        if ($jugador->equipos()->count() > 0) {
            return back()->with('error', 'Ya estás en un equipo. Debes salirte del equipo actual antes de unirte a otro.');
        }

        // Verificamos si ya está en este equipo
        if ($equipo->jugadores()->where('idJugador', $jugadorId)->exists()) {
            return back()->with('error', 'Ya estás en este equipo');
        }

        // Se une al equipo como jugador regular
        $equipo->jugadores()->attach($jugadorId, [
            'esCapitan' => false
        ]);

        return back()->with('success', '✅ Te has unido al equipo "' . $equipo->nombreEquipo . '" correctamente');
    }

    /**
     * Salirse de un equipo
     */
    public function salirse(Request $request, Equipo $equipo)
    {
        $jugadorId = session('idPersona');
        
        if (!$jugadorId) {
            return back()->with('error', 'Debes estar logueado para salirte de un equipo');
        }

        $jugador = Jugador::find($jugadorId);
        
        if (!$jugador) {
            return back()->with('error', 'Jugador no encontrado');
        }

        // Verificamos si está en el equipo
        if (!$equipo->jugadores()->where('idJugador', $jugadorId)->exists()) {
            return back()->with('error', 'No estás en este equipo');
        }

        // Verificamos si es capitán
        $esCapitan = $equipo->jugadores()->where('idJugador', $jugadorId)->first()->pivot->esCapitan;
        
        if ($esCapitan) {
            return back()->with('error', 'No puedes salirte del equipo siendo capitán. Debes transferir el liderazgo primero.');
        }

        // Se sale del equipo
        $equipo->jugadores()->detach($jugadorId);

        return back()->with('success', '✅ Te has salido del equipo "' . $equipo->nombreEquipo . '" correctamente');
    }

    /**
     * Crear equipo y convertirse en capitán
     */
    public function crearEquipo(Request $request)
    {
        $jugadorId = session('idPersona');
        
        if (!$jugadorId) {
            return back()->with('error', 'Debes estar logueado para crear un equipo');
        }

        $jugador = Jugador::find($jugadorId);
        
        if (!$jugador) {
            return back()->with('error', 'Jugador no encontrado');
        }

        // Verificamos si ya está en algún equipo
        if ($jugador->equipos()->count() > 0) {
            return back()->with('error', 'Ya estás en un equipo. Debes salirte del equipo actual antes de crear uno nuevo.');
        }

        $request->validate([
            'nombreEquipo' => 'required|string|max:100|unique:equipo,nombreEquipo'
        ]);

        // Creamos el equipo usando consulta SQL directa
        $equipoId = \DB::table('equipo')->insertGetId([
            'nombreEquipo' => $request->nombreEquipo
        ]);
        
        $equipo = Equipo::find($equipoId);

        // Se une al equipo como capitán
        $equipo->jugadores()->attach($jugadorId, [
            'esCapitan' => true
        ]);

        // Actualizamos el rol del usuario en la sesión y en la base de datos
        $jugador->persona->update(['rol' => 'capitan']);
        session(['rol' => 'capitan']);

        return redirect()->route('equipos.disponibles')
                        ->with('success', '✅ Equipo "' . $equipo->nombreEquipo . '" creado correctamente. Ahora eres el capitán.');
    }

    /**
     * Mostrar formulario para crear equipo
     */
    public function mostrarCrearEquipo()
    {
        $jugadorId = session('idPersona');
        
        if (!$jugadorId) {
            return back()->with('error', 'Debes estar logueado para crear un equipo');
        }

        $jugador = Jugador::find($jugadorId);
        
        if (!$jugador) {
            return back()->with('error', 'Jugador no encontrado');
        }

        // Verificamos si ya está en algún equipo
        if ($jugador->equipos()->count() > 0) {
            return back()->with('error', 'Ya estás en un equipo. Debes salirte del equipo actual antes de crear uno nuevo.');
        }

        return view('equipos.crear-equipo');
    }

    /**
     * Eliminar equipo completo (solo para capitanes)
     */
    public function eliminarEquipoCompleto(Request $request, Equipo $equipo)
    {
        $jugadorId = session('idPersona');
        
        if (!$jugadorId) {
            return back()->with('error', 'Debes estar logueado para eliminar un equipo');
        }

        $jugador = Jugador::find($jugadorId);
        
        if (!$jugador) {
            return back()->with('error', 'Jugador no encontrado');
        }

        // Verificar si el jugador está en el equipo
        if (!$equipo->jugadores()->where('idJugador', $jugadorId)->exists()) {
            return back()->with('error', 'No estás en este equipo');
        }

        // Verificamos si es capitán
        $esCapitan = $equipo->jugadores()->where('idJugador', $jugadorId)->first()->pivot->esCapitan;
        
        if (!$esCapitan) {
            return back()->with('error', 'Solo el capitán puede eliminar el equipo');
        }

        // Obtenemos el nombre del equipo antes de eliminarlo
        $nombreEquipo = $equipo->nombreEquipo;

        // Borramos todos los partidos del capitán
        $capitan = $jugador->persona;
        \App\Models\Partido::where('capitan', $capitan->nombre)->delete();

        // Borramos todos los torneos del equipo
        $equipo->torneosComoEquipo1()->delete();
        $equipo->torneosComoEquipo2()->delete();

        // Borramos todas las relaciones de jugadores con el equipo
        $equipo->jugadores()->detach();

        // Borramos el equipo
        $equipo->delete();

        // Actualizamos el rol del usuario en la base de datos y en la sesión para que sea jugador
        $jugador->persona->update(['rol' => 'jugador']);
        session(['rol' => 'jugador']);

        return redirect()->route('equipos.disponibles')
                        ->with('success', '✅ Equipo "' . $nombreEquipo . '" eliminado correctamente. Ahora eres un jugador.');
    }

    /**
     * Expulsar un jugador del equipo (solo para capitanes)
     */
    public function expulsarJugador(Request $request, Equipo $equipo)
    {
        $capitanId = session('idPersona');
        
        if (!$capitanId) {
            return back()->with('error', 'Debes estar logueado para expulsar jugadores');
        }

        $capitan = Jugador::find($capitanId);
        
        if (!$capitan) {
            return back()->with('error', 'Jugador no encontrado');
        }

        // Verificamos si el capitán está en el equipo
        if (!$equipo->jugadores()->where('idJugador', $capitanId)->exists()) {
            return back()->with('error', 'No estás en este equipo');
        }

        // Verificamos si es capitán
        $esCapitan = $equipo->jugadores()->where('idJugador', $capitanId)->first()->pivot->esCapitan;
        
        if (!$esCapitan) {
            return back()->with('error', 'Solo el capitán puede expulsar jugadores');
        }

        $request->validate([
            'idJugador' => 'required|integer|exists:jugador,idJugador'
        ]);

        $jugadorId = $request->idJugador;

        // Verificar si el jugador está en el equipo
        if (!$equipo->jugadores()->where('idJugador', $jugadorId)->exists()) {
            return back()->with('error', 'El jugador no está en este equipo');
        }

        // No permitimos que se expulse a sí mismo
        if ($jugadorId == $capitanId) {
            return back()->with('error', 'No puedes expulsarte a ti mismo');
        }

        // Expulsamos al jugador del equipo
        $equipo->jugadores()->detach($jugadorId);

        return back()->with('success', '✅ Jugador expulsado del equipo correctamente');
    }
}
