<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Marca;
use App\Models\ProductoFoto;

class Producto extends Model
{
    /** Alícuotas de IVA posibles (el precio cargado ya lo incluye) */
    public const IVAS = [0, 10.5, 21];

    protected $fillable = [
        'marca_id',
        'categoria_id',
        'orden',
        'nombre',
        'descripcion',
        'codigo',
        'precio',
        'precio_anterior',
        'iva',
        'foto',
        'archivo',
        'active',
        'destacado',
        // material público del detalle
        'informacion',
        'ficha',
        'instructivos',
        'videos_web',
    ];

    protected $casts = [
        'precio' => 'float',
        'precio_anterior' => 'float',
        'iva' => 'float',
        'active' => 'boolean',
        'destacado' => 'boolean',
        'informacion' => 'array',
        'ficha' => 'array',
        'instructivos' => 'array',
        'videos_web' => 'array',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function fotos()
    {
        return $this->hasMany(ProductoFoto::class, 'productos_id')->orderBy('orden');
    }

    public function scopeActivos($query)
    {
        return $query->where('active', true);
    }

    // ---------------------------------------------------------------
    // Precios
    // ---------------------------------------------------------------

    /** Sin precio cargado se muestra "Consultar" y no se puede agregar al carrito */
    public function esConsultar(): bool
    {
        return !$this->precio || $this->precio <= 0;
    }

    public function enOferta(): bool
    {
        return !$this->esConsultar() && $this->precio_anterior && $this->precio_anterior > $this->precio;
    }

    public function tieneIva(): bool
    {
        return (float) $this->iva > 0;
    }

    /** Precio sin el IVA del producto (el precio cargado lo incluye) */
    public function precioSinIva(?float $precio = null): float
    {
        $precio ??= (float) $this->precio;

        return round($precio / (1 + ((float) $this->iva) / 100), 2);
    }

    /** "$410.000,00" */
    public static function formatearPrecio(?float $valor): string
    {
        return '$' . number_format((float) $valor, 2, ',', '.');
    }

    /** Foto principal: la del producto o la primera de la galería */
    public function fotoPrincipal(): ?string
    {
        return $this->foto ?: $this->fotos->firstWhere('active', true)?->foto;
    }

    /** Fotos para la galería del detalle: principal + galería activa */
    public function galeria(): array
    {
        return collect([$this->foto])
            ->merge($this->fotos->where('active', true)->pluck('foto'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
