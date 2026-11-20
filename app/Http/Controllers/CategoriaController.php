<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Admin: árbol de categorías del catálogo (hasta 3 niveles).
 */
class CategoriaController extends Controller
{
    public function index()
    {
        return view('content.admin.categorias', [
            'arbol' => Categoria::arbol(false),
            // Solo pueden ser "padre" las de nivel 1 y 2 (así el árbol no pasa de 3 niveles)
            'padres' => Categoria::opcionesParaSelect(null, Categoria::NIVELES - 1),
            'conteos' => Producto::selectRaw('categoria_id, count(*) as total')->groupBy('categoria_id')->pluck('total', 'categoria_id'),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        Categoria::create($datos + ['active' => true]);

        return back()->with('success', 'Categoría creada.');
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update($this->validar($request, $categoria));

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        // Las subcategorías se borran en cascada; los productos quedan sin categoría
        $categoria->delete();

        return back()->with('success', 'Categoría eliminada.');
    }

    public function switch($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update(['active' => !$categoria->active]);

        return back();
    }

    private function validar(Request $request, ?Categoria $categoria = null): array
    {
        $validator = validator($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'orden' => ['nullable', 'string', 'max:20'],
            'parent_id' => ['nullable', 'integer', Rule::exists('categorias', 'id')],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        $validator->after(function (Validator $v) use ($request, $categoria) {
            if (!$request->filled('parent_id')) {
                return;
            }
            $padre = Categoria::find($request->parent_id);
            if (!$padre) {
                return;
            }
            if ($categoria && in_array($padre->id, $categoria->idsConDescendientes())) {
                $v->errors()->add('parent_id', 'Una categoría no puede quedar dentro de sí misma.');
                return;
            }
            // nivel del padre + 1 + profundidad de las hijas que arrastra
            $profundidadHijas = $categoria ? $this->profundidad($categoria) - 1 : 0;
            if ($padre->nivel() + 1 + $profundidadHijas > Categoria::NIVELES) {
                $v->errors()->add('parent_id', 'El catálogo admite hasta ' . Categoria::NIVELES . ' niveles.');
            }
        });

        $datos = $validator->validate();
        $datos['parent_id'] = $datos['parent_id'] ?? null;

        return $datos;
    }

    private function profundidad(Categoria $categoria): int
    {
        $hijas = $categoria->hijas()->get();

        return 1 + ($hijas->isEmpty() ? 0 : $hijas->max(fn ($h) => $this->profundidad($h)));
    }
}
