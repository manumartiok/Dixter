<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class WebUsuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'web_usuarios';

protected $fillable = [
    'usuario', 'email', 'contraseña', 'rol', 'active',
    'empresa', 'cuit', 'telefono', 'domicilio',
    'codigo_postal', 'localidad', 'provincia', 'pais',
];


    public function getAuthPassword()
    {
        return $this->contraseña;
    }

    public function favoritos()
    {
        return $this->belongsToMany(Producto::class, 'favoritos', 'web_usuario_id', 'producto_id')->withTimestamps();
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'usuario_id');
    }
}
