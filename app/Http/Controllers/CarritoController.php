<?php

namespace App\Http\Controllers;

use App\Models\Info;
use App\Models\Producto;
use App\Services\Carrito;
use Illuminate\Http\Request;

/**
 * Carrito de compras (requiere cuenta: middleware web.usuario.auth).
 */
class CarritoController extends Controller
{
    public function index()
    {
        $carrito = Carrito::delUsuarioActual();
        $lineas = $carrito->lineas();

        return view('content.web.carrito', [
            'lineas' => $lineas,
            'totales' => $carrito->totales($lineas),
            'info' => Info::first(),
            'envioElegido' => session('compra.envio', 'retiro_local'),
        ]);
    }

    /** HTML del mini carrito (panel lateral) */
    public function mini()
    {
        return response()->json($this->respuesta(Carrito::delUsuarioActual()));
    }

    public function agregar(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => 'required|integer|exists:productos,id',
            'cantidad' => 'nullable|integer|min:1|max:' . Carrito::MAX_CANTIDAD,
        ]);

        $producto = Producto::findOrFail($datos['producto_id']);
        if (!$producto->active || $producto->esConsultar()) {
            return response()->json(['success' => false, 'message' => 'Este producto se vende a pedido: consultanos por precio y disponibilidad.'], 422);
        }

        $carrito = Carrito::delUsuarioActual();
        $carrito->agregar($producto, (int) ($datos['cantidad'] ?? 1));

        return response()->json(['success' => true] + $this->respuesta($carrito));
    }

    public function actualizar(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => 'required|integer',
            'cantidad' => 'required|integer|min:0|max:' . Carrito::MAX_CANTIDAD,
        ]);

        $carrito = Carrito::delUsuarioActual();
        $carrito->actualizar((int) $datos['producto_id'], (int) $datos['cantidad']);

        return response()->json(['success' => true] + $this->respuesta($carrito));
    }

    public function quitar(Request $request)
    {
        $datos = $request->validate(['producto_id' => 'required|integer']);

        $carrito = Carrito::delUsuarioActual();
        $carrito->quitar((int) $datos['producto_id']);

        return response()->json(['success' => true] + $this->respuesta($carrito));
    }

    public function favorito(Producto $producto)
    {
        $usuario = auth('web_usuario')->user();
        $resultado = $usuario->favoritos()->toggle($producto->id);

        return response()->json(['success' => true, 'favorito' => !empty($resultado['attached'])]);
    }

    /** Datos que necesita el front después de cualquier cambio */
    private function respuesta(Carrito $carrito): array
    {
        $lineas = $carrito->lineas();
        $totales = $carrito->totales($lineas);

        return [
            'cantidad' => $lineas->count(),
            'totales' => $totales,
            'mini' => view('components.mini-carrito', compact('lineas', 'totales'))->render(),
            'resumen' => view('components.carrito-resumen', ['totales' => $totales, 'info' => Info::first(), 'envioElegido' => session('compra.envio', 'retiro_local')])->render(),
            'lineasHtml' => view('components.carrito-lineas', compact('lineas'))->render(),
        ];
    }
}
