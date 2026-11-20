<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Categoría del catálogo. Árbol de hasta 3 niveles mediante parent_id.
 */
class Categoria extends Model
{
    public const NIVELES = 3;

    protected $fillable = ['parent_id', 'orden', 'nombre', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function padre()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function hijas()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('orden')->orderBy('nombre');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    /** Raíz → ... → esta categoría (para las migas de pan) */
    public function ancestros(): array
    {
        $cadena = [];
        $actual = $this;
        while ($actual) {
            array_unshift($cadena, $actual);
            $actual = $actual->padre;
        }

        return $cadena;
    }

    public function nivel(): int
    {
        return count($this->ancestros());
    }

    /** Ids de esta categoría y todas sus descendientes (para filtrar productos) */
    public function idsConDescendientes(?Collection $todas = null): array
    {
        $todas ??= self::all(['id', 'parent_id']);
        $ids = [$this->id];
        $pendientes = [$this->id];

        while ($pendientes) {
            $hijas = $todas->whereIn('parent_id', $pendientes)->pluck('id')->all();
            $ids = array_merge($ids, $hijas);
            $pendientes = $hijas;
        }

        return $ids;
    }

    /** Árbol completo de categorías activas, ordenado, con las hijas ya cargadas */
    public static function arbol(bool $soloActivas = true): Collection
    {
        $todas = self::query()
            ->when($soloActivas, fn ($q) => $q->where('active', true))
            ->orderBy('orden')->orderBy('nombre')
            ->get();

        $porPadre = $todas->groupBy(fn ($c) => $c->parent_id ?? 0);
        foreach ($todas as $categoria) {
            $categoria->setRelation('hijas', $porPadre->get($categoria->id, new Collection()));
        }

        return $porPadre->get(0, new Collection());
    }

    /** Lista plana con sangría para los selects del admin: [id => '— — Nombre'] */
    public static function opcionesParaSelect(?int $excluirId = null, int $maxNivel = self::NIVELES): array
    {
        $opciones = [];
        $recorrer = function ($categorias, $nivel) use (&$recorrer, &$opciones, $excluirId, $maxNivel) {
            foreach ($categorias as $categoria) {
                if ($categoria->id === $excluirId || $nivel > $maxNivel) {
                    continue;
                }
                $opciones[$categoria->id] = str_repeat('— ', $nivel - 1) . $categoria->nombre;
                $recorrer($categoria->hijas, $nivel + 1);
            }
        };
        $recorrer(self::arbol(false), 1);

        return $opciones;
    }
}
