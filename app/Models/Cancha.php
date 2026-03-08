<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cancha extends Model
{
    protected $table = 'cancha';
    protected $primaryKey = 'idCancha';
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'disponibilidad'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    /**
     * Relación con los partidos
     */
    public function partidos(): HasMany
    {
        return $this->hasMany(Partido::class, 'cancha', 'idCancha');
    }

    /**
     * Relación con los torneos
     */
    public function torneos(): HasMany
    {
        return $this->hasMany(Torneo::class, 'cancha', 'idCancha');
    }

    /**
     * Scope para canchas disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('estado', true)
                    ->where('disponibilidad', 'Disponible');
    }

    /**
     * Scope para canchas ocupadas
     */
    public function scopeOcupadas($query)
    {
        return $query->where('estado', false)
                    ->orWhere('disponibilidad', '!=', 'Disponible');
    }
}
