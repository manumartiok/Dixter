<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GestionaArchivos;
use App\Models\Familia;
use Illuminate\Http\Request;

class FamiliaController extends Controller
{
    use GestionaArchivos;

    public function index()
    {
        $items = Familia::orderBy('orden')->get();

        return view('content.admin.familia', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('content.admin.familia-creador', [
            'item' => new Familia(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validar($request);

        $item = new Familia();
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.familia')
            ->with('success', 'Registro creado correctamente.');
    }

    public function show($id)
    {
        return view('content.admin.familia-editor', [
            'item' => Familia::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validar($request);

        $item = Familia::findOrFail($id);
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.familia')
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $item = Familia::findOrFail($id);
        $this->borrarArchivosDe($item, ['foto'], []);
        $item->delete();

        return redirect()->route('adm.familia')
            ->with('success', 'Registro eliminado correctamente.');
    }

    public function switch($id)
    {
        $item = Familia::findOrFail($id);
        $item->active = !$item->active;
        $item->save();

        return redirect()->route('adm.familia')
            ->with('success', 'Estado actualizado correctamente.');
    }

    public function destacado($id)
    {
        $item = Familia::findOrFail($id);
        $item->destacado = !$item->destacado;
        $item->save();

        return redirect()->route('adm.familia')
            ->with('success', 'Destacado actualizado correctamente.');
    }

    public function reordenar(Request $request)
    {
        foreach ($request->orden as $fila) {
            Familia::where('id', $fila['id'])->update(['orden' => $fila['orden']]);
        }

        return response()->json(['success' => true]);
    }

    private function validar(Request $request): void
    {
        $request->validate([
            'foto' => 'nullable|image|max:2048',
        ]);
    }

    private function completar(Familia $item, Request $request): void
    {
        $item->orden = $request->input('orden');
        $item->nombre = $request->input('nombre');
        $item->descripcion = $request->input('descripcion');
        $item->foto = $this->procesarArchivo($request, 'foto', $item->foto, 'familias');
        $item->link = $request->input('link');
    }
}
