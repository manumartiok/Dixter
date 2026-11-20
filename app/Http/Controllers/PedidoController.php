<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin: histórico de pedidos de la tienda.
 * El carrito y la compra de la web están en CarritoController y CompraController.
 */
class PedidoController extends Controller
{
    public function adminIndex(Request $request)
    {
        $buscar = trim((string) $request->get('q', ''));

        $pedidos = Pedido::with(['usuario', 'items'])
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                    if (ctype_digit(ltrim($buscar, '#'))) {
                        $query->orWhere('id', (int) ltrim($buscar, '#'));
                    }
                    $query->orWhere('nombre', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%")
                        ->orWhere('cuit', 'like', "%{$buscar}%")
                        ->orWhere('razon_social', 'like', "%{$buscar}%")
                        ->orWhereHas('items', fn ($items) => $items
                            ->where('codigo', 'like', "%{$buscar}%")
                            ->orWhere('nombre', 'like', "%{$buscar}%"));
                });
            })
            ->latest()->paginate(15)->withQueryString();

        return view('content.admin.historico', compact('pedidos'));
    }

    public function adminShow(Pedido $pedido)
    {
        $pedido->load(['usuario', 'items']);

        return view('content.admin.historico-editor', compact('pedido'));
    }

    public function adminUpdate(Request $request)
    {
        $validated = $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'fecha_entrega' => 'nullable|date',
            'estado' => ['required', Rule::in(array_keys(Pedido::ESTADOS))],
        ]);

        Pedido::findOrFail($validated['pedido_id'])->update([
            'fecha_entrega' => $validated['fecha_entrega'] ?? null,
            'estado' => $validated['estado'],
        ]);

        return redirect()->route('adm.pedidos-historico-editor', $validated['pedido_id'])
            ->with('success', 'Pedido actualizado correctamente.');
    }

    public function adminDestroy(Pedido $pedido)
    {
        $pedido->delete();

        return redirect()->route('adm.pedidos-historico')->with('success', 'Pedido eliminado correctamente.');
    }

    /** Cambio rápido de estado desde el listado */
    public function adminEstado(Request $request, Pedido $pedido)
    {
        $validated = $request->validate([
            'estado' => ['required', Rule::in(array_keys(Pedido::ESTADOS))],
        ]);

        $pedido->update(['estado' => $validated['estado']]);

        return back()->with('success', "Pedido {$pedido->numero()}: {$pedido->estado_texto}.");
    }
}
