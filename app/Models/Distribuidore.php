<?php

namespace App\Models;

use App\Models\Concerns\Traducible;
use Illuminate\Database\Eloquent\Model;

class Distribuidore extends Model
{
    use Traducible;

    protected $fillable = [
        'orden',
        'nombre',
        'direccion',
        'localidad',
        'provincia',
        'web',
        'email',
        'telefono',
        'latitud',
        'longitud',
        'lineas',
        'active',
    ];

    /** Líneas de productos: clave => [texto, color del nombre, archivo del ícono] (Figma: Nuestra red > Referencias) */
    public const LINEAS = [
        'limpieza' => ['Limpieza', '#000000'],
        'turbinas' => ['Turbinas', '#E58C92'],
        'pulidoras' => ['Pulidoras', '#820D0F'],
        'pulverizadores' => ['Pulverizadores', '#F95F38'],
    ];

    /** Distribuidor con más de una línea (o todas): ícono y color de "Todas las líneas" */
    public const TODAS = ['Todas las líneas', '#D50110'];

    protected $casts = [
        'active' => 'boolean',
        'lineas' => 'array',
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    /** Clave del ícono/marcador: una sola línea => esa línea; varias => 'todas' */
    public function claveLinea(): string
    {
        $lineas = array_values(array_intersect($this->lineas ?? [], array_keys(self::LINEAS)));

        return count($lineas) === 1 ? $lineas[0] : 'todas';
    }

    public function colorLinea(): string
    {
        $clave = $this->claveLinea();

        return $clave === 'todas' ? self::TODAS[1] : self::LINEAS[$clave][1];
    }

    // Campos que se guardan por idioma (según config/sitio.php)
    protected array $traducibles = [];
}
