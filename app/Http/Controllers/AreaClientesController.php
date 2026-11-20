<?php

namespace App\Http\Controllers;

use App\Models\ProductoCliente;
use Illuminate\Support\Facades\Auth;

/**
 * Área clientes: "Mis productos" muestra todos los productos activos del Área clientes
 * (no son los de la tienda) con su advertencia, videos y manuales/documentos.
 */
class AreaClientesController extends Controller
{
    public function productos($productoId = null)
    {
        $cliente = Auth::guard('cliente')->user();

        $productos = ProductoCliente::where('active', true)->orderBy('nombre')->get();

        $producto = $productoId
            ? $productos->firstWhere('id', (int) $productoId)
            : $productos->first();

        // Un producto inexistente o inactivo no se puede ver
        abort_if($productoId && !$producto, 404);

        return view('content.web.area-clientes', compact('cliente', 'productos', 'producto'));
    }
}
