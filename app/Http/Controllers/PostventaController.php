<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GestionaArchivos;
use App\Models\Postventa;
use Illuminate\Http\Request;

class PostventaController extends Controller
{
    use GestionaArchivos;

    public function show()
    {
        return view('content.admin.postventa', [
            'item' => Postventa::firstOrCreate([]),
        ]);
    }

    public function update(Request $request)
    {
        $item = Postventa::firstOrCreate([]);
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.postventa')
            ->with('success', 'Cambios guardados correctamente.');
    }

    private function completar(Postventa $item, Request $request): void
    {
        $item->etiqueta = $request->input('etiqueta');
        $item->titulo = $request->input('titulo');
    }
}
