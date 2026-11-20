<?php

namespace App\Services;

use App\Mail\PedidoEstadoMail;
use App\Models\Contacto;
use App\Models\Info;
use App\Models\Pedido;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Mails de cada estado del pedido (documento "Web 2026 Pedidos Detalles"):
 *
 *   En espera   al crearse el pedido               cliente + casilla de ventas
 *   Procesando  a mano desde el admin              cliente
 *   Despachado  a mano desde el admin              cliente
 *   Falló       al rechazarse el pago (o a mano)   cliente + casilla de ventas
 *   Completado  a mano desde el admin              cliente
 *   Cancelado   el cliente o el admin              cliente + casilla de ventas
 *
 * Lo llama PedidoObserver cada vez que se guarda un cambio de estado.
 */
class AvisosDePedido
{
    public const DESTINOS = [
        'en_espera' => ['cliente', 'dixter'],
        'procesando' => ['cliente'],
        'despachado' => ['cliente'],
        'fallido' => ['cliente', 'dixter'],
        'completado' => ['cliente'],
        'cancelado' => ['cliente', 'dixter'],
    ];

    /** Casilla de ventas configurada en Tienda > Info. de compra (o, si falta, el mail de Contacto) */
    public static function casillaVentas(): ?string
    {
        return Info::first()?->mail_ventas ?: Contacto::first()?->mail;
    }

    public function enviar(Pedido $pedido): void
    {
        $estado = (string) $pedido->estado;
        $destinos = self::DESTINOS[$estado] ?? [];
        $pedido->load('items.producto.fotos');

        if (in_array('cliente', $destinos, true) && filled($pedido->email)) {
            $this->mandar($pedido, fn () => Mail::to($pedido->email)
                ->send(new PedidoEstadoMail($pedido, $estado, false, $pedido->canceladoPorCliente)));
        }

        $ventas = self::casillaVentas();
        if (in_array('dixter', $destinos, true) && $ventas) {
            $cc = Info::first()?->mail_ventas_cc;
            $this->mandar($pedido, fn () => Mail::to($ventas)
                ->cc($cc ? [$cc] : [])
                ->send(new PedidoEstadoMail($pedido, $estado, true, $pedido->canceladoPorCliente)));
        }
    }

    // Un mail que no sale no corta el pedido ni el cambio de estado
    private function mandar(Pedido $pedido, callable $envio): void
    {
        try {
            $envio();
        } catch (Throwable $e) {
            Log::error("No se pudo enviar el mail del pedido {$pedido->id} ({$pedido->estado}): " . $e->getMessage());
        }
    }
}
