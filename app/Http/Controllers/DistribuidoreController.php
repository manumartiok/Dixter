<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GestionaArchivos;
use App\Models\Banner;
use App\Models\Distribuidore;
use Illuminate\Http\Request;

class DistribuidoreController extends Controller
{
    use GestionaArchivos;

    public function index()
    {
        $items = Distribuidore::orderBy('orden')->get();

        return view('content.admin.distribuidore', [
            'items' => $items,
            'encabezado' => Banner::seccion('nuestra-red'),
        ]);
    }

    /** Título de la página Nuestra red (se guarda en el banner de la sección) */
    public function encabezado(Request $request)
    {
        $request->validate(['texto' => 'nullable|string|max:255']);

        $banner = Banner::seccion('nuestra-red');
        $banner->texto = $request->input('texto');
        $banner->save();

        return redirect()->route('adm.distribuidore')->with('success', 'Título actualizado correctamente.');
    }

    public function create()
    {
        return view('content.admin.distribuidore-creador', [
            'item' => new Distribuidore(),
        ]);
    }

    public function store(Request $request)
    {
        $item = new Distribuidore();
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.distribuidore')
            ->with('success', 'Registro creado correctamente.');
    }

    public function show($id)
    {
        return view('content.admin.distribuidore-editor', [
            'item' => Distribuidore::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Distribuidore::findOrFail($id);
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.distribuidore')
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $item = Distribuidore::findOrFail($id);
        $this->borrarArchivosDe($item, [], []);
        $item->delete();

        return redirect()->route('adm.distribuidore')
            ->with('success', 'Registro eliminado correctamente.');
    }

    public function switch($id)
    {
        $item = Distribuidore::findOrFail($id);
        $item->active = !$item->active;
        $item->save();

        return redirect()->route('adm.distribuidore')
            ->with('success', 'Estado actualizado correctamente.');
    }

    public function reordenar(Request $request)
    {
        foreach ($request->orden as $fila) {
            Distribuidore::where('id', $fila['id'])->update(['orden' => $fila['orden']]);
        }

        return response()->json(['success' => true]);
    }

    private function completar(Distribuidore $item, Request $request): void
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'provincia' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'lineas' => 'nullable|array',
            'lineas.*' => 'in:' . implode(',', array_keys(Distribuidore::LINEAS)),
        ], ['nombre.required' => 'El nombre es obligatorio.']);

        $item->orden = $request->input('orden');
        $item->nombre = $request->input('nombre');
        $item->direccion = $request->input('direccion');
        $item->localidad = $request->input('localidad');
        $item->provincia = $request->input('provincia');
        $item->web = $request->input('web');
        $item->email = $request->input('email');
        $item->telefono = $request->input('telefono');
        $item->latitud = $request->input('latitud');
        $item->longitud = $request->input('longitud');
        $item->lineas = array_values($request->input('lineas', []));
    }
}
