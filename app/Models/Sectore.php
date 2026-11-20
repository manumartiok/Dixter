<?php

namespace App\Models;

use App\Models\Concerns\Traducible;
use Illuminate\Database\Eloquent\Model;

class Sectore extends Model
{
    use Traducible;

    protected $fillable = [
        'orden',
        'icono',
        'nombre',
        'descripcion',
        'link',
        'destacado',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'destacado' => 'boolean',
    ];

    // Campos que se guardan por idioma (según config/sitio.php)
    protected array $traducibles = [];
}
