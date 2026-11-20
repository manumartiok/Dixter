<?php

namespace App\Models;

use App\Models\Concerns\Traducible;
use Illuminate\Database\Eloquent\Model;

class Soporte extends Model
{
    use Traducible;

    protected $fillable = [
        'etiqueta',
        'titulo',
        'texto',
        'items',
        'cta_etiqueta',
        'cta_titulo',
        'cta_boton',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    // Campos que se guardan por idioma (según config/sitio.php)
    protected array $traducibles = [];
}
