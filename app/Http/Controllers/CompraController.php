<?php

namespace App\Http\Controllers;

use App\Models\Info;
use App\Models\Pedido;
use App\Services\Carrito;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Checkout: datos de facturación, envío, forma de pago (en local o Mercado Pago), confirmación
 * y cancelación desde Mi cuenta. Los mails de cada estado los manda PedidoObserver.
 */
class CompraController extends Controller
{
    public function checkout(Request $request, MercadoPagoService $mercadoPago)
    {
        $carrito = Carrito::delUsuarioActual();
        $lineas = $carrito->lineas();
        if ($lineas->isEmpty()) {
            return redirect()->route('web-carrito');
        }

        // El envío se elige en el carrito y se recuerda en la sesión
        if (array_key_exists((string) $request->query('envio'), Pedido::ENVIOS)) {
            session(['compra.envio' => $request->query('envio')]);
        }

        $usuario = auth('web_usuario')->user();
        $ultimo = $usuario->pedidos()->latest()->first();

        return view('content.web.compra', [
            'lineas' => $lineas,
            'totales' => $carrito->totales($lineas),
            'envio' => session('compra.envio', 'retiro_local'),
            'info' => Info::first(),
            'mercadoPago' => $mercadoPago->configurado(),
            // Datos precargados: los del último pedido o los de la cuenta
            'previo' => [
                'nombre' => $ultimo->nombre ?? $usuario->usuario,
                'dni' => $ultimo->dni ?? null,
                'cuit' => $ultimo->cuit ?? $usuario->cuit,
                'razon_social' => $ultimo->razon_social ?? ($usuario->razon_social ?: $usuario->empresa),
                'email' => $ultimo->email ?? $usuario->email,
                'celular' => $ultimo->celular ?? $usuario->telefono,
                'direccion' => $ultimo->direccion ?? $usuario->domicilio,
                'localidad' => $ultimo->localidad ?? $usuario->localidad,
                'provincia' => $ultimo->provincia ?? $usuario->provincia,
                'codigo_postal' => $ultimo->codigo_postal ?? $usuario->codigo_postal,
            ],
        ]);
    }

    public function realizar(Request $request, MercadoPagoService $mercadoPago)
    {
        $pagos = array_keys(Pedido::PAGOS);
        if (!$mercadoPago->configurado()) {
            $pagos = array_values(array_diff($pagos, ['mercado_pago']));
        }

        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20',
            'cuit' => 'required|string|max:20',
            'razon_social' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'celular' => 'required|string|max:50',
            'direccion' => 'required|string|max:255',
            'localidad' => 'required|string|max:255',
            'provincia' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:20',
            'otra_direccion' => 'nullable|boolean',
            'otra.direccion' => 'required_if:otra_direccion,1|nullable|string|max:255',
            'otra.localidad' => 'required_if:otra_direccion,1|nullable|string|max:255',
            'otra.provincia' => 'required_if:otra_direccion,1|nullable|string|max:255',
            'otra.codigo_postal' => 'required_if:otra_direccion,1|nullable|string|max:20',
            'mensaje' => 'nullable|string|max:2000',
            'envio' => ['required', Rule::in(array_keys(Pedido::ENVIOS))],
            'forma_pago' => ['required', Rule::in($pagos)],
            'terminos' => 'accepted',
        ], [
            'required' => 'Completá este dato.',
            'required_if' => 'Completá este dato.',
            'email' => 'Ingresá un e-mail válido.',
            'terminos.accepted' => 'Tenés que aceptar los términos y condiciones.',
            'forma_pago.in' => 'Elegí una forma de pago.',
        ]);

        $usuario = auth('web_usuario')->user();
        $carrito = Carrito::delUsuarioActual();
        $lineas = $carrito->lineas();
        if ($lineas->isEmpty()) {
            return redirect()->route('web-carrito');
        }
        $totales = $carrito->totales($lineas);

        $pedido = DB::transaction(function () use ($datos, $usuario, $lineas, $totales, $carrito) {
            $pedido = Pedido::create([
                'usuario_id' => $usuario->id,
                'estado' => Pedido::ESTADO_INICIAL,
                'entrega' => $datos['envio'],
                'forma_pago' => $datos['forma_pago'],
                'pago_estado' => $datos['forma_pago'] === 'mercado_pago' ? 'pendiente' : null,
                'mensaje' => $datos['mensaje'] ?? null,
                'subtotal' => $totales['subtotal'],
                'iva' => $totales['iva'],
                'total' => $totales['total'],
                'entrega_otra' => !empty($datos['otra_direccion']) ? array_filter($datos['otra'] ?? []) : null,
            ] + collect($datos)->only([
                'nombre', 'dni', 'cuit', 'razon_social', 'email', 'celular', 'direccion', 'localidad', 'provincia', 'codigo_postal',
            ])->all());

            foreach ($lineas as $linea) {
                $pedido->items()->create([
                    'producto_id' => $linea->producto->id,
                    'codigo' => $linea->producto->codigo,
                    'nombre' => $linea->producto->nombre,
                    'cantidad' => $linea->cantidad,
                    'precio_base' => $linea->precio,
                    'precio_con_descuento' => $linea->precio,
                    'iva' => $linea->producto->iva,
                    'subtotal' => $linea->total,
                ]);
            }

            $carrito->vaciar();

            return $pedido;
        });

        session()->forget('compra.envio');

        // El pedido nace "En espera" y ya mandó sus mails (al cliente y a ventas), también con Mercado Pago
        if ($pedido->forma_pago === 'mercado_pago') {
            return redirect()->route('compra.mp.pagar', $pedido);
        }

        return redirect()->route('compra.confirmado', $pedido);
    }

    public function confirmado(Pedido $pedido, MercadoPagoService $mercadoPago)
    {
        $this->autorizar($pedido);

        // Sin webhook (localhost), al volver a esta pantalla se consulta el estado del pago
        if ($pedido->forma_pago === 'mercado_pago' && $pedido->pago_estado !== 'aprobado' && $mercadoPago->configurado()) {
            $this->sincronizar($pedido, $mercadoPago);
        }

        return view('content.web.compra-confirmada', ['pedido' => $pedido->fresh('items')]);
    }

    public function pagarMercadoPago(Pedido $pedido, MercadoPagoService $mercadoPago)
    {
        $this->autorizar($pedido);
        abort_unless($pedido->forma_pago === 'mercado_pago', 404);

        if ($pedido->pago_estado === 'aprobado' || $pedido->estado === 'cancelado') {
            return redirect()->route('compra.confirmado', $pedido);
        }

        try {
            $preferencia = $mercadoPago->crearPreferencia($pedido, $pedido->email, route('compra.mp.retorno', $pedido));
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('compra.confirmado', $pedido)
                ->with('error_mp', 'No pudimos conectarnos con Mercado Pago. Intentá nuevamente en unos minutos.');
        }

        return redirect()->away($preferencia->init_point);
    }

    public function retornoMercadoPago(Request $request, Pedido $pedido, MercadoPagoService $mercadoPago)
    {
        $this->autorizar($pedido);

        $paymentId = (string) ($request->query('payment_id') ?? $request->query('collection_id'));
        $this->sincronizar($pedido, $mercadoPago, ctype_digit($paymentId) ? (int) $paymentId : null);

        return redirect()->route('compra.confirmado', $pedido);
    }

    /** Mi cuenta: el cliente cancela su pedido mientras está "En espera"; después tiene que comunicarse con Dixter */
    public function cancelar(Pedido $pedido)
    {
        $this->autorizar($pedido);

        if (!$pedido->cancelable_por_cliente) {
            return redirect()->route('web.cuenta')->with('error_cuenta',
                "El pedido {$pedido->numero()} está en estado «{$pedido->estado_texto}» y ya no se puede cancelar desde acá. Comunicate con Dixter.");
        }

        $pedido->canceladoPorCliente = true;
        $pedido->update(['estado' => 'cancelado']);

        return redirect()->route('web.cuenta')->with('ok_cuenta', "Cancelaste el pedido {$pedido->numero()}. Te enviamos un mail con el detalle.");
    }

    private function sincronizar(Pedido $pedido, MercadoPagoService $mercadoPago, ?int $paymentId = null): void
    {
        try {
            $mercadoPago->sincronizar($pedido, $paymentId);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function autorizar(Pedido $pedido): void
    {
        abort_unless($pedido->usuario_id === auth('web_usuario')->id(), 404);
    }
}
