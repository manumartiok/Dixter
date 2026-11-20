<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Services\AvisosDePedido;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

/**
 * Manda el mail del estado cada vez que se guarda un pedido nuevo o un cambio de estado
 * (compra, Mercado Pago, admin o cancelación desde Mi cuenta). Corre después del commit,
 * así el pedido nuevo ya tiene sus productos.
 */
class PedidoObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(private AvisosDePedido $avisos)
    {
    }

    public function created(Pedido $pedido): void
    {
        $this->avisos->enviar($pedido);
    }

    public function updated(Pedido $pedido): void
    {
        // "En espera" solo se avisa al crearse el pedido ("Recibimos tu pedido..."):
        // volver a ese estado (un reintento de pago aprobado o a mano) no manda mails
        if ($pedido->wasChanged('estado') && $pedido->estado !== Pedido::ESTADO_INICIAL) {
            $this->avisos->enviar($pedido);
        }
    }
}
