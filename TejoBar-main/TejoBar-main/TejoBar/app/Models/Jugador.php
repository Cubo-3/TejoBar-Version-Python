<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jugador extends Model
{
    protected $table = 'jugador';
    protected $primaryKey = 'idPersona';
    public $timestamps = false;

    protected $fillable = [
        'idPersona',
        'estado',
        'rut'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    /**
     * Relación con la tabla persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'idPersona', 'idPersona');
    }

    /**
     * Relación muchos a muchos con equipos
     */
    public function equipos(): BelongsToMany
    {
        return $this->belongsToMany(
            Equipo::class,
            'jugador_equipo',
            'idJugador',
            'idEquipo'
        )->withPivot('esCapitan');
    }

    /**
     * Relación con las compras
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class, 'idJugador', 'idPersona');
    }
}
