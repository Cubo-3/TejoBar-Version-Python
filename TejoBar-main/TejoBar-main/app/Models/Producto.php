<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'idProducto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'precio',
        'stock',
        'fechaVencimiento',
        'urlImg'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
        'fechaVencimiento' => 'date'
    ];

    /**
     * Relación con los apartados
     */
    public function apartados(): HasMany
    {
        return $this->hasMany(Apartado::class, 'idProducto', 'idProducto');
    }

    /**
     * Scope para productos disponibles (stock > 0)
     */
    public function scopeDisponibles($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope para productos próximos a vencer
     */
    public function scopeProximosAVencer($query, $dias = 30)
    {
        return $query->where('fechaVencimiento', '<=', now()->addDays($dias));
    }
}
