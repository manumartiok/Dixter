<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Throwable;

/**
 * Notificaciones de pagos de Mercado Pago (webhook). Igual que en BBS.
 * Responde 204 aunque el pago no sea de un pedido, así Mercado Pago no reintenta.
 */
class MercadoPagoController extends Controller
{
    public function webhook(Request $request, MercadoPagoService $mercadoPago)
    {
        $tipo = $request->input('type') ?? $request->query('topic');
        $paymentId = (string) ($request->query('data_id') ?? $request->input('data.id') ?? $request->query('id'));

        if ($tipo !== 'payment' || !ctype_digit($paymentId)) {
            return response()->noContent();
        }
        if (!$mercadoPago->firmaValida($request, $paymentId)) {
            return response()->noContent(401);
        }

        try {
            $pago = $mercadoPago->obtenerPago((int) $paymentId);
            $pedido = $mercadoPago->pedidoDeReferencia($pago->external_reference);

            // aplicarPago actualiza el pedido; si el pago falló, PedidoObserver manda los mails de "Falló"
            if ($pedido) {
                $mercadoPago->aplicarPago($pedido, $pago);
            }
        } catch (Throwable $e) {
            report($e);
        }

        return response()->noContent();
    }
}
