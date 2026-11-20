<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GestionaArchivos;
use App\Models\Soporte;
use Illuminate\Http\Request;

class SoporteController extends Controller
{
    use GestionaArchivos;

    public function show()
    {
        return view('content.admin.soporte', [
            'item' => Soporte::firstOrCreate([]),
        ]);
    }

    public function update(Request $request)
    {
        $this->validar($request);

        $item = Soporte::firstOrCreate([]);
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.soporte')
            ->with('success', 'Cambios guardados correctamente.');
    }

    private function validar(Request $request): void
    {
        $request->validate([
            'items.*.icono' => 'nullable|image|max:2048',
        ]);
    }

    private function completar(Soporte $item, Request $request): void
    {
        $item->etiqueta = $request->input('etiqueta');
        $item->titulo = $request->input('titulo');
        $item->texto = $request->input('texto');
        $item->items = $this->procesarLista($request, 'items', ['icono' => 'imagen', 'titulo' => 'string', 'texto' => 'text'], $item->items, 'soportes');
        $item->cta_etiqueta = $request->input('cta_etiqueta');
        $item->cta_titulo = $request->input('cta_titulo');
        $item->cta_boton = $request->input('cta_boton');
    }
}
