{{-- Contenido del mini carrito (Figma: Carrito 493x621). $lineas, $totales --}}
@use('App\Models\Producto')
<div class="flex items-center justify-between border-b border-linea pb-[20px]">
    <h2 class="text-[24px] font-semibold leading-[1.2] text-[#17181b]">Carrito</h2>
    <button type="button" data-cerrar-mini aria-label="Cerrar carrito" class="p-1">
        <img src="{{ asset('assets/img/dixter/icon-close.svg') }}" alt="" width="21" height="21" class="size-[21px] rotate-45">
    </button>
</div>

@if ($lineas->isEmpty())
    <p class="py-[40px] text-center text-[16px]">Tu carrito está vacío.</p>
@else
    <ul class="max-h-[50vh] overflow-y-auto">
        @foreach ($lineas as $linea)
            <li class="border-b border-linea py-[24px]">
                <div class="flex gap-[14px]">
                    <a href="{{ route('productos.detalle', $linea->producto) }}" class="size-[62px] shrink-0 overflow-hidden rounded-[2px] border border-linea bg-[#fafcfc]">
                        @if ($foto = $linea->producto->fotoPrincipal())<img src="{{ $foto }}" alt="" class="size-full object-contain mix-blend-multiply">@endif
                    </a>
                    <a href="{{ route('productos.detalle', $linea->producto) }}" class="flex-1 text-[20px] font-semibold leading-[1.2] text-[#17181b]">{{ $linea->producto->nombre }}</a>
                    <button type="button" data-quitar="{{ $linea->producto->id }}" aria-label="Quitar {{ $linea->producto->nombre }}" class="h-fit p-1">
                        <img src="{{ asset('assets/img/dixter/icon-trash-mini.svg') }}" alt="" width="20" height="24">
                    </button>
                </div>
                <div class="mt-[20px] flex items-start justify-between gap-4 border-t border-linea pt-[24px] pl-[76px]">
                    @include('components.cantidad', ['productoId' => $linea->producto->id, 'cantidad' => $linea->cantidad])
                    <div class="text-right">
                        <p class="text-[24px] font-extrabold leading-[1.2]">{{ Producto::formatearPrecio($linea->total) }}</p>
                        @if ($linea->producto->tieneIva())
                            <p class="text-[14px] leading-[1.5] text-[#17181b]/60">{{ Producto::formatearPrecio($linea->total_sin_iva) }} sin IVA</p>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>

    <div class="flex items-start justify-between border-b border-linea py-[24px]">
        <p class="text-[24px] font-semibold leading-[1.2] text-[#17181b]">Total</p>
        <div class="text-right">
            <p class="text-[24px] font-extrabold leading-[1.2]">{{ Producto::formatearPrecio($totales['total']) }}</p>
            @if ($totales['muestra_iva'])
                <p class="text-[14px] leading-[1.5] text-[#17181b]/60">{{ Producto::formatearPrecio($totales['subtotal']) }} sin IVA</p>
            @endif
        </div>
    </div>

    <div class="mt-[24px] flex flex-col gap-[12px]">
        <a href="{{ route('web-carrito') }}" class="flex items-center justify-center rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d]">Ver carrito</a>
        <button type="button" data-cerrar-mini class="rounded-[4px] border border-marca bg-white p-[10px] text-[16px] leading-[1.5] text-marca transition hover:bg-marca hover:text-white">Seguir comprando</button>
    </div>
@endif
