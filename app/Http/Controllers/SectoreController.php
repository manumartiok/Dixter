<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GestionaArchivos;
use App\Models\Sectore;
use Illuminate\Http\Request;

class SectoreController extends Controller
{
    use GestionaArchivos;

    public function index()
    {
        $items = Sectore::orderBy('orden')->get();

        return view('content.admin.sectore', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('content.admin.sectore-creador', [
            'item' => new Sectore(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validar($request);

        $item = new Sectore();
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.sectore')
            ->with('success', 'Registro creado correctamente.');
    }

    public function show($id)
    {
        return view('content.admin.sectore-editor', [
            'item' => Sectore::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validar($request);

        $item = Sectore::findOrFail($id);
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.sectore')
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $item = Sectore::findOrFail($id);
        $this->borrarArchivosDe($item, ['icono'], []);
        $item->delete();

        return redirect()->route('adm.sectore')
            ->with('success', 'Registro eliminado correctamente.');
    }

    public function switch($id)
    {
        $item = Sectore::findOrFail($id);
        $item->active = !$item->active;
        $item->save();

        return redirect()->route('adm.sectore')
            ->with('success', 'Estado actualizado correctamente.');
    }

    /** Destacado = se muestra en el Home */
    public function destacar($id)
    {
        $item = Sectore::findOrFail($id);
        $item->destacado = !$item->destacado;
        $item->save();

        return redirect()->route('adm.sectore')
            ->with('success', 'Destacado actualizado correctamente.');
    }

    public function reordenar(Request $request)
    {
        foreach ($request->orden as $fila) {
            Sectore::where('id', $fila['id'])->update(['orden' => $fila['orden']]);
        }

        return response()->json(['success' => true]);
    }

    private function validar(Request $request): void
    {
        $request->validate([
            'icono' => 'nullable|image|max:2048',
        ]);
    }

    private function completar(Sectore $item, Request $request): void
    {
        $item->orden = $request->input('orden');
        $item->icono = $this->procesarArchivo($request, 'icono', $item->icono, 'sectores');
        $item->nombre = $request->input('nombre');
        $item->descripcion = $request->input('descripcion');
        $item->link = $request->input('link');
        $item->destacado = $request->boolean('destacado');
    }
}
