<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Torneo extends Model
{
    protected $table = 'torneo';
    protected $primaryKey = 'idPartido';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'equipo1',
        'equipo2',
        'cancha'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    /**
     * Relación con el equipo 1
     */
    public function equipo1(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo1', 'idEquipo');
    }

    /**
     * Relación con el equipo 2
     */
    public function equipo2(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo2', 'idEquipo');
    }

    /**
     * Relación con la cancha
     */
    public function cancha(): BelongsTo
    {
        return $this->belongsTo(Cancha::class, 'cancha', 'idCancha');
    }

    /**
     * Scope para torneos de una fecha específica
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    /**
     * Scope para torneos de un equipo específico
     */
    public function scopePorEquipo($query, $equipoId)
    {
        return $query->where('equipo1', $equipoId)
                    ->orWhere('equipo2', $equipoId);
    }

    /**
     * Scope para torneos de una cancha específica
     */
    public function scopePorCancha($query, $canchaId)
    {
        return $query->where('cancha', $canchaId);
    }

    /**
     * Scope para torneos futuros
     */
    public function scopeFuturos($query)
    {
        return $query->where('fecha', '>', now());
    }

    /**
     * Scope para torneos pasados
     */
    public function scopePasados($query)
    {
        return $query->where('fecha', '<', now());
    }
}
