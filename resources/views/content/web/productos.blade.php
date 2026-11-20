@extends('layouts.web')

@section('title', ($categoria?->nombre ? $categoria->nombre . ' · ' : '') . 'Productos · Dixter')

@php
    $fmt = fn ($n) => '$ ' . number_format((int) $n, 0, ',', '.');
    $urlCategoria = fn ($c) => route('productos', array_filter(['categoria' => $c?->id, 'min' => $min, 'max' => $max], fn ($v) => $v !== null));
@endphp

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[100px] md:px-[71px]">
    {{-- Migas --}}
    <p class="pt-[24px] text-[12px] leading-[1.5]">
        <a href="{{ route('home') }}" class="font-bold">Inicio</a> &gt;
        @if ($categoria)
            <a href="{{ route('productos') }}" class="font-bold">Productos</a>
            @foreach ($categoria->ancestros() as $i => $miga)
                &gt; @if ($loop->last){{ $miga->nombre }}@else<a href="{{ $urlCategoria($miga) }}" class="font-bold">{{ $miga->nombre }}</a>@endif
            @endforeach
        @else
            Productos
        @endif
    </p>

    <div class="mt-[40px] grid grid-cols-1 items-start gap-[24px] md:mt-[62px] lg:grid-cols-[288px_1fr]">

        {{-- ===== Filtros ===== --}}
        <aside class="flex flex-col gap-[16px]" aria-label="Filtros">
            <button type="button" id="toggle-filtros" class="flex items-center justify-between rounded-[6px] border border-linea px-[16px] py-[12px] text-[18px] lg:hidden" aria-expanded="false" aria-controls="filtros">
                Filtrar productos <img src="{{ asset('assets/img/dixter/icon-chevron-down-sm.svg') }}" alt="" width="18" height="18">
            </button>

            <div id="filtros" class="hidden flex-col gap-[16px] lg:flex">
                @foreach ($arbol as $nivel1)
                    @php $abierta1 = in_array($nivel1->id, $abiertas); @endphp
                    <div class="categoria-caja rounded-[6px] border border-linea px-[16px] {{ $abierta1 ? 'abierta pb-[14px]' : '' }}">
                        <div class="flex min-h-[52px] items-center justify-between gap-2">
                            <a href="{{ $urlCategoria($nivel1) }}"
                               class="py-[12px] text-[20px] leading-[1.5] hover:text-marca {{ $abierta1 ? 'font-bold' : 'font-light' }}">{{ $nivel1->nombre }}</a>
                            @if ($nivel1->hijas->isNotEmpty())
                                <button type="button" data-plegar aria-expanded="{{ $abierta1 ? 'true' : 'false' }}" aria-label="Ver subcategorías de {{ $nivel1->nombre }}" class="p-1">
                                    <img src="{{ asset('assets/img/dixter/icon-chevron-down-sm.svg') }}" alt="" width="18" height="18" class="chevron transition-transform {{ $abierta1 ? 'rotate-180' : '' }}">
                                </button>
                            @endif
                        </div>

                        @if ($nivel1->hijas->isNotEmpty())
                            <ul class="plegable border-t border-linea pt-[8px] {{ $abierta1 ? '' : 'hidden' }}">
                                @foreach ($nivel1->hijas as $nivel2)
                                    @php $abierta2 = in_array($nivel2->id, $abiertas); @endphp
                                    <li>
                                        <div class="flex items-center justify-between gap-2">
                                            <a href="{{ $urlCategoria($nivel2) }}"
                                               class="text-[16px] leading-[2] hover:text-marca {{ $abierta2 ? 'font-bold' : '' }}">{{ $nivel2->nombre }}</a>
                                            @if ($nivel2->hijas->isNotEmpty())
                                                <button type="button" data-plegar aria-expanded="{{ $abierta2 ? 'true' : 'false' }}" aria-label="Ver subcategorías de {{ $nivel2->nombre }}" class="p-1">
                                                    <img src="{{ asset('assets/img/dixter/icon-chevron-down-sm.svg') }}" alt="" width="18" height="18" class="chevron transition-transform {{ $abierta2 ? 'rotate-180' : '' }}">
                                                </button>
                                            @endif
                                        </div>
                                        @if ($nivel2->hijas->isNotEmpty())
                                            <ul class="plegable pb-[4px] pl-[10px] {{ $abierta2 ? '' : 'hidden' }}">
                                                @foreach ($nivel2->hijas as $nivel3)
                                                    <li>
                                                        <a href="{{ $urlCategoria($nivel3) }}"
                                                           class="text-[14px] leading-[2] hover:text-marca {{ $categoria?->id === $nivel3->id ? 'font-bold' : '' }}">{{ $nivel3->nombre }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach

                {{-- Filtro de precio --}}
                @if ($rangoMax > $rangoMin)
                    @php
                        $desde = $min ?? $rangoMin;
                        $hasta = $max ?? $rangoMax;
                    @endphp
                    <form method="GET" action="{{ route('productos') }}" id="filtro-precio" class="flex flex-col gap-[20px] rounded-[6px] border border-linea px-[16px] pb-[16px] pt-[12px]">
                        @if ($categoria)<input type="hidden" name="categoria" value="{{ $categoria->id }}">@endif
                        <div class="flex items-center justify-between">
                            <p class="text-[20px] font-light">Precio</p>
                            @if ($min !== null || $max !== null)
                                <a href="{{ route('productos', array_filter(['categoria' => $categoria?->id])) }}" class="text-[12px] text-marca underline">Limpiar</a>
                            @endif
                        </div>

                        <div class="rango relative h-[16px]" data-min="{{ $rangoMin }}" data-max="{{ $rangoMax }}">
                            <div class="absolute inset-x-0 top-1/2 h-[2px] -translate-y-1/2 rounded bg-linea"></div>
                            <div class="rango-activo absolute top-1/2 h-[2px] -translate-y-1/2 rounded bg-marca"></div>
                            <input type="range" name="min" min="{{ $rangoMin }}" max="{{ $rangoMax }}" step="1000" value="{{ $desde }}" aria-label="Precio mínimo">
                            <input type="range" name="max" min="{{ $rangoMin }}" max="{{ $rangoMax }}" step="1000" value="{{ $hasta }}" aria-label="Precio máximo">
                        </div>

                        <div class="flex items-center gap-[8px]">
                            <div class="flex-1 rounded-[4px] border border-linea px-[10px] py-[6px]">
                                <p class="text-[11px] text-[#737378]">Mínimo</p>
                                <p class="text-[14px] font-semibold" data-salida="min">{{ $fmt($desde) }}</p>
                            </div>
                            <span class="text-[14px] text-[#737378]">–</span>
                            <div class="flex-1 rounded-[4px] border border-linea px-[10px] py-[6px]">
                                <p class="text-[11px] text-[#737378]">Máximo</p>
                                <p class="text-[14px] font-semibold" data-salida="max">{{ $fmt($hasta) }}</p>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </aside>

        {{-- ===== Productos ===== --}}
        <section aria-label="Productos">
            @if ($categoria)
                <h1 class="sr-only">{{ $categoria->nombre }}</h1>
            @else
                <h1 class="sr-only">Productos</h1>
            @endif

            @if ($productos->isEmpty())
                <div class="rounded-[4px] border border-linea bg-gris/5 px-[24px] py-[40px] text-center text-[16px]">
                    No encontramos productos con estos filtros.
                    <a href="{{ route('productos') }}" class="mt-2 block font-semibold text-marca underline">Ver todos los productos</a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-[24px] sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($productos as $producto)
                        @include('components.producto-card', ['producto' => $producto])
                    @endforeach
                </div>

                @if ($productos->hasPages())
                    <nav class="mt-[40px] flex items-center justify-center gap-[6px]" aria-label="Páginas">
                        @if (!$productos->onFirstPage())
                            <a href="{{ $productos->previousPageUrl() }}" class="flex size-[40px] items-center justify-center rounded-[4px] border border-linea hover:border-marca" aria-label="Anterior">‹</a>
                        @endif
                        @foreach ($productos->getUrlRange(1, $productos->lastPage()) as $pagina => $url)
                            <a href="{{ $url }}" class="flex size-[40px] items-center justify-center rounded-[4px] border {{ $pagina === $productos->currentPage() ? 'border-marca bg-marca text-white' : 'border-linea hover:border-marca' }}">{{ $pagina }}</a>
                        @endforeach
                        @if ($productos->hasMorePages())
                            <a href="{{ $productos->nextPageUrl() }}" class="flex size-[40px] items-center justify-center rounded-[4px] border border-linea hover:border-marca" aria-label="Siguiente">›</a>
                        @endif
                    </nav>
                @endif
            @endif
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Slider doble de precio (Figma: Filtro Precio > Rango) */
    .rango input[type=range] { position: absolute; inset: 0; width: 100%; margin: 0; background: none; pointer-events: none; -webkit-appearance: none; appearance: none; }
    .rango input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; pointer-events: auto; width: 16px; height: 16px; border-radius: 50%; background: #fff; border: 2px solid #C32922; cursor: pointer; }
    .rango input[type=range]::-moz-range-thumb { pointer-events: auto; width: 12px; height: 12px; border-radius: 50%; background: #fff; border: 2px solid #C32922; cursor: pointer; }
    .rango input[type=range]::-webkit-slider-runnable-track { background: none; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Plegar / desplegar categorías
    document.querySelectorAll('[data-plegar]').forEach((boton) => {
        boton.addEventListener('click', () => {
            const contenedor = boton.closest('li') || boton.closest('.categoria-caja');
            const lista = contenedor.querySelector(':scope > .plegable');
            const abierta = lista.classList.toggle('hidden') === false;
            boton.setAttribute('aria-expanded', abierta);
            boton.querySelector('.chevron').classList.toggle('rotate-180', abierta);
        });
    });

    // Filtros en mobile
    document.getElementById('toggle-filtros')?.addEventListener('click', function () {
        const filtros = document.getElementById('filtros');
        const abierto = filtros.classList.toggle('hidden') === false;
        filtros.classList.toggle('flex', abierto);
        this.setAttribute('aria-expanded', abierto);
    });

    // Slider de precio: muestra los valores y filtra al soltar
    const form = document.getElementById('filtro-precio');
    if (form) {
        const rango = form.querySelector('.rango');
        const [inMin, inMax] = rango.querySelectorAll('input[type=range]');
        const activo = rango.querySelector('.rango-activo');
        const total = rango.dataset.max - rango.dataset.min;
        const fmt = (n) => '$ ' + Number(n).toLocaleString('es-AR');
        const pintar = () => {
            if (+inMin.value > +inMax.value) [inMin.value, inMax.value] = [inMax.value, inMin.value];
            const a = (inMin.value - rango.dataset.min) / total * 100;
            const b = (inMax.value - rango.dataset.min) / total * 100;
            activo.style.left = a + '%';
            activo.style.width = (b - a) + '%';
            form.querySelector('[data-salida=min]').textContent = fmt(inMin.value);
            form.querySelector('[data-salida=max]').textContent = fmt(inMax.value);
        };
        [inMin, inMax].forEach((input) => {
            input.addEventListener('input', pintar);
            input.addEventListener('change', () => form.submit());
        });
        pintar();
    }
});
</script>
@endpush
