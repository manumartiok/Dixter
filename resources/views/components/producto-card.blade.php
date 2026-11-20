{{-- Tarjeta de producto (Figma: Producto / Producto SALE). $producto, $favoritos (ids) --}}
@php
    $esFavorito = in_array($producto->id, $favoritos ?? []);
    $foto = $producto->fotoPrincipal();
@endphp
<article class="group relative flex h-full flex-col overflow-hidden rounded-[4px] border border-linea bg-white transition hover:shadow-[0_0_10px_rgba(0,0,0,0.12)]">
    <a href="{{ route('productos.detalle', $producto) }}" class="block aspect-square overflow-hidden rounded-t-[3px] bg-white">
        @if ($foto)
            <img src="{{ $foto }}" alt="{{ $producto->nombre }}" loading="lazy" class="size-full object-contain transition-transform duration-500 group-hover:scale-105">
        @endif
    </a>

    {{-- Hover (Figma: Producto / Variant2): carrito y lupa centrados sobre la foto --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 flex aspect-square items-center justify-center gap-[12px] opacity-0 transition-opacity duration-200 group-hover:opacity-100 group-focus-within:opacity-100">
        @unless ($producto->esConsultar())
            <form data-agregar-carrito class="pointer-events-auto">
                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                <input type="hidden" name="cantidad" value="1">
                <button type="submit" aria-label="Agregar {{ $producto->nombre }} al carrito"
                        class="flex size-[36px] items-center justify-center rounded-[4px] border border-marca bg-white transition hover:bg-marca/10 disabled:opacity-60">
                    <img src="{{ asset('assets/img/dixter/icon-card-cart.svg') }}" alt="" width="16" height="16">
                </button>
            </form>
        @endunless
        <a href="{{ route('productos.detalle', $producto) }}" aria-label="Ver {{ $producto->nombre }}"
           class="pointer-events-auto flex size-[36px] items-center justify-center rounded-[4px] border border-black bg-white transition hover:bg-black/5">
            <img src="{{ asset('assets/img/dixter/icon-card-search.svg') }}" alt="" width="16" height="16">
        </a>
    </div>

    @if ($producto->enOferta())
        <span class="absolute left-0 top-[23px] rounded-r-[4px] bg-marca px-[10px] py-[2px] text-[12px] font-bold leading-[1.5] text-white">OFERTA</span>
    @endif

    <button type="button" data-favorito="{{ $producto->id }}" aria-pressed="{{ $esFavorito ? 'true' : 'false' }}"
            aria-label="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
            class="favorito absolute right-[24px] top-[24px] size-[24px] transition hover:scale-110 {{ $esFavorito ? 'activo' : '' }}">
        @include('components.icono-corazon')
    </button>

    <a href="{{ route('productos.detalle', $producto) }}" class="flex flex-1 flex-col px-[24px]">
        <span class="border-t border-linea pt-[14px] text-[12px] font-bold leading-[1.5] text-marca">
            @if ($producto->codigo) SKU: {{ $producto->codigo }} @else &nbsp; @endif
        </span>
        <h3 class="mt-[4px] min-h-[48px] text-[20px] font-semibold leading-[1.2] text-black">{{ $producto->nombre }}</h3>

        <div class="mt-auto border-t border-linea pb-[23px] pt-[12px] text-right">
            @if ($producto->esConsultar())
                <p class="pt-[23px] text-[26px] font-extrabold leading-[1.2]">Consultar</p>
            @else
                <p class="h-[19px] text-[16px] leading-[1.2] text-black/50 line-through">
                    {{ $producto->enOferta() ? \App\Models\Producto::formatearPrecio($producto->precio_anterior) : '' }}
                </p>
                <p class="mt-[4px] text-[26px] font-extrabold leading-[1.2]">{{ \App\Models\Producto::formatearPrecio($producto->precio) }}</p>
            @endif
        </div>
    </a>
</article>
