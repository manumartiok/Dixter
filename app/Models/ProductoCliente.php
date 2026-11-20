<?php

namespace App\Models;

use App\Models\Concerns\Traducible;
use Illuminate\Database\Eloquent\Model;

/**
 * Producto del Área clientes: no es el mismo que el de la tienda. Todos los clientes ven todos los activos.
 * Tiene sus videos subidos (titulo | archivo) y manuales/documentos descargables (titulo | archivo).
 */
class ProductoCliente extends Model
{
    use Traducible;

    protected $fillable = [
        'nombre',
        'advertencia',
        'videos',
        'documentos',
        'active',
    ];

    protected $casts = [
        'videos' => 'array',
        'documentos' => 'array',
        'active' => 'boolean',
    ];

    // Campos que se guardan por idioma (según config/sitio.php)
    protected array $traducibles = [];

    /** Videos y documentos completos (sin filas vacías) */
    public function videosCargados(): array
    {
        return array_values(array_filter($this->videos ?? [], fn ($v) => !empty($v['archivo'])));
    }

    public function documentosCargados(): array
    {
        return array_values(array_filter($this->documentos ?? [], fn ($d) => !empty($d['archivo'])));
    }

}
