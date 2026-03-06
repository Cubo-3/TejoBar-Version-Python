<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historial extends Model
{
    protected $table = 'historial';
    protected $primaryKey = 'idHistorial';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'idPersona',
        'idProducto',
        'cantidad',
        'precio',
        'total',
        'fechaEntrega',
        'estado'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2',
        'total' => 'decimal:2',
        'fechaEntrega' => 'datetime'
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
}
