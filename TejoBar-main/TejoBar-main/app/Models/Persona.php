<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Persona extends Model
{
    protected $table = 'persona';
    protected $primaryKey = 'idPersona';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'nombre',
        'correo',
        'contrasena',
        'numero',
        'rol'
    ];

    protected $casts = [
        'rol' => 'string'
    ];

    protected $hidden = [
        'contrasena'
    ];

    /**
     * Relación con la tabla jugador
     */
    public function jugador(): HasOne
    {
        return $this->hasOne(Jugador::class, 'idPersona', 'idPersona');
    }

    /**
     * Relación con los apartados
     */
    public function apartados(): HasMany
    {
        return $this->hasMany(Apartado::class, 'idPersona', 'idPersona');
    }
}
