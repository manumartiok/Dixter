<?php

namespace App\Models;

use App\Models\Concerns\Traducible;
use Illuminate\Database\Eloquent\Model;

class Familia extends Model
{
    use Traducible;

    protected $fillable = [
        'orden',
        'nombre',
        'descripcion',
        'foto',
        'link',
        'active',
        'destacado',
    ];

    protected $casts = [
        'active' => 'boolean',
        'destacado' => 'boolean',
    ];

    // Campos que se guardan por idioma (según config/sitio.php)
    protected array $traducibles = [];
}
