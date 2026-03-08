<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Compra extends Model
{
    protected $table = 'compra';
    protected $primaryKey = 'idCompra';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'fecha',
        'total',
        'idJugador'
    ];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2'
    ];

    /**
     * Relación con la tabla jugador
     */
    public function jugador(): BelongsTo
    {
        return $this->belongsTo(Jugador::class, 'idJugador', 'idPersona');
    }

    /**
     * Scope para compras de una fecha específica
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    /**
     * Scope para compras de un rango de fechas
     */
    public function scopePorRangoFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para compras con total mayor a un valor
     */
    public function scopeConTotalMayorA($query, $total)
    {
        return $query->where('total', '>', $total);
    }
}
