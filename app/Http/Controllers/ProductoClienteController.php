<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\GestionaArchivos;
use App\Models\ProductoCliente;
use Illuminate\Http\Request;

class ProductoClienteController extends Controller
{
    use GestionaArchivos;

    private const VIDEOS = ['titulo' => 'string', 'archivo' => 'video'];
    private const DOCUMENTOS = ['titulo' => 'string', 'archivo' => 'archivo'];

    public function index()
    {
        $items = ProductoCliente::orderBy('nombre')->get();

        return view('content.admin.producto-cliente', [
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view('content.admin.producto-cliente-creador', [
            'item' => new ProductoCliente(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validar($request);

        $item = new ProductoCliente();
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.producto-cliente')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show($id)
    {
        return view('content.admin.producto-cliente-editor', [
            'item' => ProductoCliente::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validar($request);

        $item = ProductoCliente::findOrFail($id);
        $this->completar($item, $request);
        $item->save();

        return redirect()->route('adm.producto-cliente')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $item = ProductoCliente::findOrFail($id);
        $this->borrarArchivosDe($item, [], ['videos' => self::VIDEOS, 'documentos' => self::DOCUMENTOS]);
        $item->delete();

        return redirect()->route('adm.producto-cliente')
            ->with('success', 'Producto eliminado correctamente.');
    }

    public function switch($id)
    {
        $item = ProductoCliente::findOrFail($id);
        $item->active = !$item->active;
        $item->save();

        return redirect()->route('adm.producto-cliente')
            ->with('success', 'Estado actualizado correctamente.');
    }

    private function validar(Request $request): void
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'advertencia' => 'nullable|string|max:1000',
            'videos.*.titulo' => 'nullable|string|max:255',
            'videos.*.archivo' => 'nullable|file|mimetypes:video/mp4,video/webm,video/quicktime|max:204800',
            'documentos.*.titulo' => 'nullable|string|max:255',
            'documentos.*.archivo' => 'nullable|file|max:20480',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'videos.*.archivo.mimetypes' => 'Los videos tienen que ser MP4, WebM o MOV.',
            'videos.*.archivo.max' => 'Cada video puede pesar hasta 200 MB.',
            'documentos.*.archivo.max' => 'Cada archivo puede pesar hasta 20 MB.',
        ]);
    }

    private function completar(ProductoCliente $item, Request $request): void
    {
        $item->nombre = $request->input('nombre');
        $item->advertencia = $request->input('advertencia');
        $item->videos = $this->procesarLista($request, 'videos', self::VIDEOS, $item->videos, 'area-clientes/videos');
        $item->documentos = $this->procesarLista($request, 'documentos', self::DOCUMENTOS, $item->documentos, 'area-clientes/documentos');
    }
}
