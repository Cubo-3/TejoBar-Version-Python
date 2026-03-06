<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $table = 'equipo';
    protected $primaryKey = 'idEquipo';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'nombreEquipo'
    ];

    /**
     * Relación muchos a muchos con jugadores
     */
    public function jugadores(): BelongsToMany
    {
        return $this->belongsToMany(
            Jugador::class,
            'jugador_equipo',
            'idEquipo',
            'idJugador'
        )->withPivot('esCapitan');
    }

    /**
     * Relación con torneos como equipo1
     */
    public function torneosComoEquipo1(): HasMany
    {
        return $this->hasMany(Torneo::class, 'equipo1', 'idEquipo');
    }

    /**
     * Relación con torneos como equipo2
     */
    public function torneosComoEquipo2(): HasMany
    {
        return $this->hasMany(Torneo::class, 'equipo2', 'idEquipo');
    }

    /**
     * Obtener todos los torneos del equipo (como equipo1 o equipo2)
     */
    public function torneos()
    {
        return Torneo::where('equipo1', $this->idEquipo)
                    ->orWhere('equipo2', $this->idEquipo);
    }
}
