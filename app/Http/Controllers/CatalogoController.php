<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Info;
use App\Models\Producto;
use Illuminate\Http\Request;

/**
 * Catálogo público: listado con el árbol de categorías + filtro de precio, y detalle del producto.
 */
class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $arbol = Categoria::arbol();
        $categoria = $request->filled('categoria')
            ? Categoria::where('active', true)->find($request->integer('categoria'))
            : null;

        // Rango de precios disponible (para el slider)
        $rango = Producto::activos()->where('precio', '>', 0)
            ->selectRaw('MIN(precio) as minimo, MAX(precio) as maximo')->first();
        $rangoMin = (int) floor($rango->minimo ?? 0);
        $rangoMax = (int) ceil($rango->maximo ?? 0);

        $min = $request->filled('min') ? max(0, $request->integer('min')) : null;
        $max = $request->filled('max') ? max(0, $request->integer('max')) : null;

        $productos = Producto::activos()
            ->with('fotos')
            ->when($categoria, fn ($q) => $q->whereIn('categoria_id', $categoria->idsConDescendientes()))
            // con filtro de precio, los "Consultar" (sin precio) quedan afuera
            ->when($min !== null, fn ($q) => $q->where('precio', '>=', $min))
            ->when($max !== null, fn ($q) => $q->where('precio', '<=', $max))
            ->orderByRaw('orden IS NULL, orden')
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view('content.web.productos', [
            'arbol' => $arbol,
            'categoria' => $categoria,
            'abiertas' => $categoria ? collect($categoria->ancestros())->pluck('id')->all() : [$arbol->first()?->id],
            'productos' => $productos,
            'rangoMin' => $rangoMin,
            'rangoMax' => $rangoMax,
            'min' => $min,
            'max' => $max,
            'favoritos' => $this->favoritosDelUsuario(),
        ]);
    }

    public function show(Producto $producto)
    {
        abort_unless($producto->active, 404);
        $producto->load(['fotos', 'categoria']);

        // Tabla "Modelos": este producto + los de su misma categoría que tengan ficha
        $modelos = collect([$producto]);
        if ($producto->categoria_id) {
            $modelos = $modelos->merge(
                Producto::activos()
                    ->where('categoria_id', $producto->categoria_id)
                    ->where('id', '!=', $producto->id)
                    ->orderByRaw('orden IS NULL, orden')->orderBy('nombre')
                    ->get()
                    ->filter(fn ($p) => !empty($p->ficha))
                    ->take(3)
            );
        }
        $modelos = $modelos->filter(fn ($p) => !empty($p->ficha))->values();
        $filasModelos = $modelos->flatMap(fn ($p) => collect($p->ficha)->pluck('nombre'))->filter()->unique()->values();

        // Relacionados: misma categoría o, si no alcanza, la categoría padre
        $relacionados = collect();
        if ($producto->categoria) {
            $ids = ($producto->categoria->padre ?? $producto->categoria)->idsConDescendientes();
            $relacionados = Producto::activos()->with('fotos')
                ->whereIn('categoria_id', $ids)
                ->where('id', '!=', $producto->id)
                ->orderByRaw('categoria_id = ? DESC', [$producto->categoria_id])
                ->orderByRaw('orden IS NULL, orden')
                ->take(4)
                ->get();
        }

        return view('content.web.producto-detalle', [
            'producto' => $producto,
            'migas' => $producto->categoria?->ancestros() ?? [],
            'modelos' => $modelos,
            'filasModelos' => $filasModelos,
            'relacionados' => $relacionados,
            'info' => Info::first(),
            'favoritos' => $this->favoritosDelUsuario(),
        ]);
    }

    private function favoritosDelUsuario(): array
    {
        $usuario = auth('web_usuario')->user();

        return $usuario ? $usuario->favoritos()->pluck('productos.id')->all() : [];
    }
}
