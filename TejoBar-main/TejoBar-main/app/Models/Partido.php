<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partido extends Model
{
    protected $table = 'partido';
    protected $primaryKey = 'idPartido';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'fecha',
        'hora',
        'capitan',
        'cancha',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado' => 'string'
    ];

    /**
     * Relación con la tabla cancha
     */
    public function cancha(): BelongsTo
    {
        return $this->belongsTo(Cancha::class, 'cancha', 'idCancha');
    }

    /**
     * Scope para partidos confirmados
     */
    public function scopeConfirmados($query)
    {
        return $query->where('estado', 'Confirmada');
    }

    /**
     * Scope para partidos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'Pendiente');
    }

    /**
     * Scope para partidos cancelados
     */
    public function scopeCancelados($query)
    {
        return $query->where('estado', 'Cancelada');
    }

    /**
     * Scope para partidos de una fecha específica
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    /**
     * Scope para partidos de una cancha específica
     */
    public function scopePorCancha($query, $canchaId)
    {
        return $query->where('cancha', $canchaId);
    }
}
