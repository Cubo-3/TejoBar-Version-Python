<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Apartado extends Model
{
    protected $table = 'apartados';
    protected $primaryKey = 'idApartado';
    public $timestamps = false;

    protected $fillable = [
        'idPersona',
        'idProducto',
        'cantidad',
        'fechaApartado',
        'estado'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'fechaApartado' => 'datetime',
        'estado' => 'string'
    ];

    /**
     * Relación con la tabla persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'idPersona', 'idPersona');
    }

    /**
     * Relación con la tabla producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'idProducto', 'idProducto');
    }

    /**
     * Scope para apartados pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para apartados comprados
     */
    public function scopeComprados($query)
    {
        return $query->where('estado', 'comprado');
    }
}
