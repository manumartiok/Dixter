{{-- Líneas de la página del carrito (Figma: Carrito, caja izquierda). $lineas --}}
@use('App\Models\Producto')
@if ($lineas->isEmpty())
    <div class="px-[22px] py-[48px] text-center">
        <p class="text-[18px]">Tu carrito está vacío.</p>
        <a href="{{ route('productos') }}" class="mt-[16px] inline-flex rounded-[4px] bg-marca px-[24px] py-[10px] text-[16px] text-white hover:bg-[#a8231d]">Ver productos</a>
    </div>
@else
    <ul>
        @foreach ($lineas as $linea)
            <li class="flex flex-col gap-[16px] border-b border-linea px-[22px] py-[32px] last:border-b-0 md:flex-row md:items-center md:gap-[24px]">
                <div class="flex flex-1 items-center gap-[24px]">
                    <a href="{{ route('productos.detalle', $linea->producto) }}" class="size-[84px] shrink-0 overflow-hidden rounded-[2px] border border-[#dcd7cd] bg-[#fafcfc]">
                        @if ($foto = $linea->producto->fotoPrincipal())<img src="{{ $foto }}" alt="" class="size-full object-contain mix-blend-multiply">@endif
                    </a>
                    <div class="max-w-[255px]">
                        @if ($linea->producto->codigo)
                            <p class="text-[12px] font-bold leading-[1.5] text-marca">SKU: {{ $linea->producto->codigo }}</p>
                        @endif
                        <a href="{{ route('productos.detalle', $linea->producto) }}" class="text-[20px] font-semibold leading-[1.2] text-[#17181b] hover:text-marca">{{ $linea->producto->nombre }}</a>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-[24px] md:justify-end">
                    @include('components.cantidad', ['productoId' => $linea->producto->id, 'cantidad' => $linea->cantidad])

                    <div class="min-w-[150px] text-right">
                        <p class="text-[26px] font-extrabold leading-[1.2]">{{ Producto::formatearPrecio($linea->total) }}</p>
                        @if ($linea->producto->tieneIva())
                            <p class="text-[14px] leading-[1.5] text-[#17181b]/60">{{ Producto::formatearPrecio($linea->total_sin_iva) }} sin IVA</p>
                        @endif
                    </div>

                    <button type="button" data-quitar="{{ $linea->producto->id }}" aria-label="Quitar {{ $linea->producto->nombre }}" class="p-1 hover:opacity-70">
                        <img src="{{ asset('assets/img/dixter/icon-trash.svg') }}" alt="" width="20" height="24">
                    </button>
                </div>
            </li>
        @endforeach
    </ul>
@endif
