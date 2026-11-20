<?php

namespace App\Models;

use App\Models\Concerns\TienePagoMercadoPago;
use App\Observers\PedidoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

/** Cada cambio de estado que se guarda manda su mail (ver PedidoObserver y AvisosDePedido) */
#[ObservedBy(PedidoObserver::class)]
class Pedido extends Model
{
    use TienePagoMercadoPago;

    /** estado => [texto, clase del badge del admin] */
    public const ESTADOS = [
        'en_espera' => ['En espera', 'bg-yellow-500'],
        'procesando' => ['Procesando', 'bg-blue-500'],
        'despachado' => ['Despachado', 'bg-indigo-500'],
        'fallido' => ['Falló', 'bg-red-500'],
        'completado' => ['Completado', 'bg-green-500'],
        'cancelado' => ['Cancelado', 'bg-gray-400'],
    ];

    /** Estado con el que nace todo pedido */
    public const ESTADO_INICIAL = 'en_espera';

    /** El cliente puede cancelar su pedido desde Mi cuenta solo mientras está en este estado */
    public const CANCELABLE_POR_CLIENTE = 'en_espera';

    /** Lo marca la cancelación desde Mi cuenta para que los mails digan quién canceló (no es columna) */
    public bool $canceladoPorCliente = false;

    /** envío => [texto, precio a mostrar] */
    public const ENVIOS = [
        'retiro_local' => ['Retiro en local', 'Gratis'],
        'envio_costo' => ['Envío con costo', 'A coordinar'],
    ];

    public const PAGOS = [
        'pago_local' => 'Pago en local',
        'mercado_pago' => 'Mercado Pago',
    ];

    protected $fillable = [
        'usuario_id',
        'cliente_id',
        'estado',
        'entrega',
        'forma_pago',
        'mensaje',
        'fecha_entrega',
        'subtotal',
        'iva',
        'total',
        'totales',
        // facturación
        'nombre',
        'dni',
        'cuit',
        'razon_social',
        'email',
        'celular',
        'direccion',
        'localidad',
        'provincia',
        'codigo_postal',
        'entrega_otra',
        // Mercado Pago
        'pago_estado',
        'mp_preference_id',
        'mp_payment_id',
        'mp_status_detail',
        'pagado_at',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'iva' => 'float',
        'total' => 'float',
        'totales' => 'array',
        'entrega_otra' => 'array',
        'fecha_entrega' => 'date',
        'pagado_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(WebUsuario::class, 'usuario_id');
    }

    public function cliente()
    {
        return $this->belongsTo(WebUsuario::class, 'cliente_id');
    }

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    /** "#00001013" */
    public function numero(): string
    {
        return '#' . str_pad((string) $this->id, 8, '0', STR_PAD_LEFT);
    }

    public function getEstadoTextoAttribute(): string
    {
        return self::ESTADOS[$this->estado][0] ?? ucfirst(str_replace('_', ' ', (string) $this->estado));
    }

    public function getEstadoBadgeAttribute(): string
    {
        return self::ESTADOS[$this->estado][1] ?? 'bg-gray-400';
    }

    public function getEnvioTextoAttribute(): string
    {
        return self::ENVIOS[$this->entrega][0] ?? (string) $this->entrega;
    }

    public function getCancelablePorClienteAttribute(): bool
    {
        return $this->estado === self::CANCELABLE_POR_CLIENTE;
    }

    public function getPagoTextoAttribute(): string
    {
        return self::PAGOS[$this->forma_pago] ?? (string) $this->forma_pago;
    }
}
