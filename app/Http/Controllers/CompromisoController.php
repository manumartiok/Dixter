<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GestionaArchivos;
use App\Models\Compromiso;
use Illuminate\Http\Request;

class CompromisoController extends Controller
{
    use GestionaArchivos;

    public function index()
    {
        $items = Compromiso::orderBy('orden')->get();

        return view('content.admin.compromiso', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('content.admin.compromiso-creador', [
            'item' => new Compromiso(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validar($request);

        $item = new Compromiso();
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.compromiso')
            ->with('success', 'Registro creado correctamente.');
    }

    public function show($id)
    {
        return view('content.admin.compromiso-editor', [
            'item' => Compromiso::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validar($request);

        $item = Compromiso::findOrFail($id);
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.compromiso')
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $item = Compromiso::findOrFail($id);
        $this->borrarArchivosDe($item, ['icono'], []);
        $item->delete();

        return redirect()->route('adm.compromiso')
            ->with('success', 'Registro eliminado correctamente.');
    }

    public function switch($id)
    {
        $item = Compromiso::findOrFail($id);
        $item->active = !$item->active;
        $item->save();

        return redirect()->route('adm.compromiso')
            ->with('success', 'Estado actualizado correctamente.');
    }

    public function reordenar(Request $request)
    {
        foreach ($request->orden as $fila) {
            Compromiso::where('id', $fila['id'])->update(['orden' => $fila['orden']]);
        }

        return response()->json(['success' => true]);
    }

    private function validar(Request $request): void
    {
        $request->validate([
            'icono' => 'nullable|image|max:2048',
        ]);
    }

    private function completar(Compromiso $item, Request $request): void
    {
        $item->orden = $request->input('orden');
        $item->icono = $this->procesarArchivo($request, 'icono', $item->icono, 'compromisos');
        $item->titulo = $request->input('titulo');
        $item->texto = $request->input('texto');
    }
}
