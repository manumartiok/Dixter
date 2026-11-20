{{--
    Mail de un pedido según su estado (App\Mail\PedidoEstadoMail).
    Réplica de los mails del documento "Web 2026 Pedidos Detalles": logo, título, saludo, resumen del pedido y direcciones.
--}}
@use('App\Models\Producto')
@use('App\Models\Pedido')
@use('App\Models\Contacto')
@use('App\Services\AvisosDePedido')
@php
    $p = $pedido;
    $ventas = AvisosDePedido::casillaVentas();
    $contacto = Contacto::first();
    $fecha = $p->created_at?->locale('es')->translatedFormat('j F, Y');
    $envioPrecio = Pedido::ENVIOS[$p->entrega][1] ?? '';

    $facturacion = array_filter([
        $p->nombre, $p->razon_social, $p->direccion, $p->localidad, $p->provincia, $p->codigo_postal, 'Argentina',
        $p->dni ? 'DNI ' . $p->dni : null, $p->cuit ? 'CUIT/CUIL ' . $p->cuit : null,
    ]);
    $otra = array_filter((array) $p->entrega_otra);
    $envio = match (true) {
        !empty($otra) => array_filter([$p->nombre, $otra['direccion'] ?? null, $otra['localidad'] ?? null, $otra['provincia'] ?? null, $otra['codigo_postal'] ?? null, 'Argentina']),
        $p->entrega === 'retiro_local' => ['Retiro en el local'],
        default => array_filter([$p->nombre, $p->direccion, $p->localidad, $p->provincia, $p->codigo_postal, 'Argentina']),
    };
    // El mail interno muestra siempre las dos direcciones; el del cliente, la de envío solo si se envía
    $mostrarEnvio = $paraDixter || $p->entrega !== 'retiro_local';

    $texto = 'font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:1.5;color:#636363;';
    $celda = 'padding:12px 0;border-bottom:1px solid #e5e5e5;vertical-align:middle;';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo }}</title>
</head>
<body style="margin:0;padding:0;background:#ffffff;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;">
<tr><td align="center" style="padding:32px 16px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;{{ $texto }}">

    {{-- Logo --}}
    <tr><td style="padding:0 0 20px;">
        <img src="{{ asset('assets/img/dixter/logo.png') }}" alt="DIXTER" width="130" style="display:block;width:130px;height:auto;border:0;">
    </td></tr>

    {{-- Título y mensaje --}}
    <tr><td>
        <h1 style="margin:0 0 24px;font-family:Helvetica,Arial,sans-serif;font-size:30px;line-height:1.2;font-weight:700;color:#1e1e1e;">{{ $titulo }}</h1>
        @unless ($paraDixter)
            <p style="margin:0 0 14px;">Hola {{ $p->nombre }},</p>
        @endunless
        @foreach ($parrafos as $parrafo)
            <p style="margin:0 0 14px;">{{ $parrafo }}</p>
        @endforeach
    </td></tr>

    {{-- Resumen del pedido --}}
    <tr><td style="padding-top:24px;">
        <h2 style="margin:0 0 4px;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:700;color:#1e1e1e;">Resumen del pedido</h2>
        <p style="margin:0 0 18px;font-size:12px;color:#1e1e1e;">Pedido {{ $p->numero() }} <span style="color:#636363;">({{ $fecha }})</span></p>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="{{ $texto }}">
            <tr>
                <th align="left" style="padding:0 0 10px;font-weight:700;color:#1e1e1e;">Producto</th>
                <th align="right" style="padding:0 0 10px;font-weight:700;color:#1e1e1e;white-space:nowrap;">Cantidad</th>
                <th align="right" style="padding:0 0 10px 16px;font-weight:700;color:#1e1e1e;">Precio</th>
            </tr>
            @foreach ($p->items as $item)
                @php $foto = $item->producto?->fotoPrincipal(); @endphp
                <tr>
                    <td style="{{ $celda }}">
                        <table role="presentation" cellpadding="0" cellspacing="0"><tr>
                            @if ($foto)
                                <td style="padding-right:14px;vertical-align:middle;">
                                    <img src="{{ url($foto) }}" alt="" width="48" height="48" style="display:block;width:48px;height:48px;object-fit:contain;border:0;">
                                </td>
                            @endif
                            <td style="vertical-align:middle;color:#1e1e1e;">
                                {{ $item->nombre }}@if ($item->codigo) <span style="color:#636363;">(#{{ $item->codigo }})</span>@endif
                            </td>
                        </tr></table>
                    </td>
                    <td align="right" style="{{ $celda }}white-space:nowrap;">×{{ rtrim(rtrim(number_format((float) $item->cantidad, 2, ',', ''), '0'), ',') }}</td>
                    <td align="right" style="{{ $celda }}padding-left:16px;white-space:nowrap;color:#1e1e1e;">{{ Producto::formatearPrecio($item->subtotal) }}</td>
                </tr>
            @endforeach
        </table>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="{{ $texto }}margin-top:18px;border-top:1px solid #e5e5e5;">
            <tr>
                <td style="padding:10px 0;">Subtotal:</td>
                <td align="right" style="padding:10px 0;">{{ Producto::formatearPrecio($p->total) }}</td>
            </tr>
            <tr>
                <td style="padding:10px 0;">Envío: {{ $p->envio_texto }}</td>
                <td align="right" style="padding:10px 0;">{{ $envioPrecio }}</td>
            </tr>
            <tr>
                <td style="padding:10px 0;font-weight:700;color:#1e1e1e;">Total:</td>
                <td align="right" style="padding:10px 0;color:#1e1e1e;">
                    <span style="font-size:17px;font-weight:700;">{{ Producto::formatearPrecio($p->total) }}</span>
                    @if ($p->iva > 0)
                        <br><span style="font-weight:700;">(incluye {{ Producto::formatearPrecio($p->iva) }} Impuestos)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="padding:10px 0;border-bottom:1px solid #e5e5e5;">Método de pago:</td>
                <td align="right" style="padding:10px 0;border-bottom:1px solid #e5e5e5;">{{ $p->pago_texto }}</td>
            </tr>
            @if ($p->mensaje)
                <tr>
                    <td style="padding:10px 0;border-bottom:1px solid #e5e5e5;vertical-align:top;">Nota:</td>
                    <td align="right" style="padding:10px 0;border-bottom:1px solid #e5e5e5;">{{ $p->mensaje }}</td>
                </tr>
            @endif
        </table>
    </td></tr>

    {{-- Direcciones --}}
    <tr><td style="padding-top:28px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="{{ $texto }}color:#1e1e1e;">
            <tr>
                <td width="50%" style="vertical-align:top;padding-right:12px;">
                    <h3 style="margin:0 0 8px;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:700;">Dirección de facturación</h3>
                    @foreach ($facturacion as $linea){{ $linea }}<br>@endforeach
                    @if ($p->celular)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $p->celular) }}" style="color:#1e1e1e;">{{ $p->celular }}</a><br>@endif
                    @if ($p->email){{ $p->email }}@endif
                </td>
                @if ($mostrarEnvio)
                    <td width="50%" style="vertical-align:top;padding-left:12px;">
                        <h3 style="margin:0 0 8px;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:700;">Dirección de envío</h3>
                        @foreach ($envio as $linea){{ $linea }}<br>@endforeach
                        @if ($p->celular && $p->entrega !== 'retiro_local')<a href="tel:{{ preg_replace('/[^0-9+]/', '', $p->celular) }}" style="color:#1e1e1e;">{{ $p->celular }}</a>@endif
                    </td>
                @endif
            </tr>
        </table>
    </td></tr>

    {{-- Completado: registrar el equipo y redes --}}
    @if (!$paraDixter && $estado === 'completado')
        <tr><td style="padding-top:32px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="{{ $texto }}background:#f5f5f5;border-radius:4px;">
                <tr><td style="padding:22px 24px;">
                    <p style="margin:0 0 12px;color:#1e1e1e;font-weight:700;">Registrá tu equipo</p>
                    <p style="margin:0 0 16px;">Registralo para acceder a la garantía y al servicio técnico de Dixter.</p>
                    <a href="{{ route('post-venta.registro') }}" style="display:inline-block;background:#C32922;color:#ffffff;text-decoration:none;font-weight:700;padding:10px 20px;border-radius:4px;">Registrar mi equipo</a>
                    @if ($contacto?->instagram || $contacto?->youtube)
                        <p style="margin:22px 0 10px;color:#1e1e1e;font-weight:700;">Seguinos</p>
                        @if ($contacto->instagram)
                            <a href="{{ $contacto->instagram }}" style="display:inline-block;margin:0 8px 8px 0;border:1px solid #C32922;color:#C32922;text-decoration:none;padding:8px 16px;border-radius:4px;">Instagram</a>
                        @endif
                        @if ($contacto->youtube)
                            <a href="{{ $contacto->youtube }}" style="display:inline-block;margin:0 8px 8px 0;border:1px solid #C32922;color:#C32922;text-decoration:none;padding:8px 16px;border-radius:4px;">YouTube</a>
                        @endif
                    @endif
                </td></tr>
            </table>
        </td></tr>
    @endif

    {{-- Cierre --}}
    <tr><td align="center" style="padding-top:40px;">
        @if ($paraDixter && $estado === 'en_espera')
            <p style="margin:0;">Felicitaciones por la venta.</p>
        @elseif ($paraDixter)
            <p style="margin:0;" align="left">Atentamente,<br>todo el Equipo de DIXTER SA</p>
        @else
            <p style="margin:0;">¡Gracias de nuevo! Si necesitás ayuda con tu pedido, contactate con nosotros{!! $ventas ? ' en <a href="mailto:' . e($ventas) . '" style="color:#636363;">' . e($ventas) . '</a>' : '' !!}.</p>
        @endif
    </td></tr>
</table>
</td></tr>
</table>
</body>
</html>
