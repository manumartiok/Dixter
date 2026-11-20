<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Usuario del "Área clientes". Lo crea el administrador; no hay registro público.
 * Inicia sesión con el guard "cliente".
 */
class Cliente extends Authenticatable
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre', 'usuario', 'email', 'contraseña', 'empresa', 'telefono', 'active',
    ];

    protected $hidden = ['contraseña', 'remember_token'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->contraseña;
    }


    /** "Mariano Rodríguez" -> "Mariano R." (botón del header) */
    public function nombreCorto(): string
    {
        $partes = preg_split('/\s+/', trim($this->nombre));
        $nombre = array_shift($partes);

        return $partes ? $nombre . ' ' . mb_strtoupper(mb_substr(end($partes), 0, 1)) . '.' : $nombre;
    }
}
