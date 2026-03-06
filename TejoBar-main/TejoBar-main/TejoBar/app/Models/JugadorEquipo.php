<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JugadorEquipo extends Model
{
    protected $table = 'jugador_equipo';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'idJugador',
        'idEquipo',
        'esCapitan'
    ];

    protected $casts = [
        'esCapitan' => 'boolean'
    ];

    /**
     * Relación con la tabla jugador
     */
    public function jugador(): BelongsTo
    {
        return $this->belongsTo(Jugador::class, 'idJugador', 'idPersona');
    }

    /**
     * Relación con la tabla equipo
     */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'idEquipo', 'idEquipo');
    }

    /**
     * Scope para capitanes
     */
    public function scopeCapitanes($query)
    {
        return $query->where('esCapitan', true);
    }

    /**
     * Scope para jugadores regulares (no capitanes)
     */
    public function scopeJugadoresRegulares($query)
    {
        return $query->where('esCapitan', false);
    }
}
