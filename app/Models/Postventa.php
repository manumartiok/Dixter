<?php

namespace App\Models;

use App\Models\Concerns\Traducible;
use Illuminate\Database\Eloquent\Model;

class Postventa extends Model
{
    use Traducible;

    protected $fillable = [
        'etiqueta',
        'titulo',
    ];

    protected $casts = [

    ];

    // Campos que se guardan por idioma (según config/sitio.php)
    protected array $traducibles = [];
}
