<?php

namespace App\Services;

use App\Models\CarritoPendiente;
use App\Models\Producto;
use App\Models\WebUsuario;
use Illuminate\Support\Collection;

/**
 * Carrito del comprador logueado. Se guarda en la base (carritos_pendientes)
 * como [producto_id => cantidad], así no se pierde al cerrar sesión.
 * Los precios siempre se leen del producto: nunca se confía en lo que manda el navegador.
 */
class Carrito
{
    public const MAX_CANTIDAD = 99;

    public function __construct(private WebUsuario $usuario)
    {
    }

    public static function delUsuarioActual(): ?self
    {
        $usuario = auth('web_usuario')->user();

        return $usuario ? new self($usuario) : null;
    }

    /** [producto_id => cantidad] */
    public function items(): array
    {
        $items = $this->registro()?->items ?? [];

        // Formato viejo de la base ([id => ['cantidad' => n, ...]]): se normaliza
        return collect($items)
            ->map(fn ($v) => is_array($v) ? (int) ($v['cantidad'] ?? 0) : (int) $v)
            ->filter(fn ($cantidad) => $cantidad > 0)
            ->all();
    }

    public function cantidadDeProductos(): int
    {
        return count($this->items());
    }

    public function agregar(Producto $producto, int $cantidad): void
    {
        $items = $this->items();
        $items[$producto->id] = min(self::MAX_CANTIDAD, ($items[$producto->id] ?? 0) + max(1, $cantidad));
        $this->guardar($items);
    }

    public function actualizar(int $productoId, int $cantidad): void
    {
        $items = $this->items();
        if ($cantidad <= 0) {
            unset($items[$productoId]);
        } else {
            $items[$productoId] = min(self::MAX_CANTIDAD, $cantidad);
        }
        $this->guardar($items);
    }

    public function quitar(int $productoId): void
    {
        $this->actualizar($productoId, 0);
    }

    public function vaciar(): void
    {
        $this->guardar([]);
    }

    /**
     * Líneas del carrito con los productos y montos calculados.
     * Los productos inactivos o sin precio ("Consultar") se descartan.
     */
    public function lineas(): Collection
    {
        $items = $this->items();
        if (!$items) {
            return collect();
        }

        $productos = Producto::with('fotos')->activos()->whereIn('id', array_keys($items))->get()->keyBy('id');

        return collect($items)
            ->map(function ($cantidad, $id) use ($productos) {
                $producto = $productos->get($id);
                if (!$producto || $producto->esConsultar()) {
                    return null;
                }

                $total = round($producto->precio * $cantidad, 2);

                return (object) [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio' => $producto->precio,
                    'total' => $total,
                    'total_sin_iva' => $producto->precioSinIva($total),
                ];
            })
            ->filter()
            ->values();
    }

    /** ['subtotal' => sin IVA, 'iva' => ..., 'total' => final, 'muestra_iva' => bool] */
    public function totales(?Collection $lineas = null): array
    {
        $lineas ??= $this->lineas();
        $total = round($lineas->sum('total'), 2);
        $sinIva = round($lineas->sum('total_sin_iva'), 2);

        return [
            'subtotal' => $sinIva,
            'iva' => round($total - $sinIva, 2),
            'total' => $total,
            'muestra_iva' => $total - $sinIva > 0.009,
        ];
    }

    private function registro(): ?CarritoPendiente
    {
        return CarritoPendiente::where('usuario_id', $this->usuario->id)->whereNull('cliente_id')->first();
    }

    private function guardar(array $items): void
    {
        if (!$items) {
            CarritoPendiente::where('usuario_id', $this->usuario->id)->whereNull('cliente_id')->delete();
            return;
        }

        CarritoPendiente::updateOrCreate(
            ['usuario_id' => $this->usuario->id, 'cliente_id' => null],
            ['items' => $items]
        );
    }
}
