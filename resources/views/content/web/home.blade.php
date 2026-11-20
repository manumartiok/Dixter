@extends('layouts.web', ['header' => 'transparente'])

@section('title', 'Dixter · Equipos para profesionales')

@php
    $home = App\Models\Home::first() ?? new App\Models\Home();
    $banner = App\Models\HomeBanner::where('active', true)->orderBy('orden')->first();
    $familias = App\Models\Familia::where('active', true)->where('destacado', true)->orderBy('orden')->take(4)->get();
    $sectores = App\Models\Sectore::where('active', true)->where('destacado', true)->orderBy('orden')->get();
    $nosotros = App\Models\Nosotro::first();
    $beneficios = $home->beneficios ?? [];
    $link = fn (string $ruta) => Route::has($ruta) ? route($ruta) : '#';
@endphp

@section('content')

{{-- ===================== HERO ===================== --}}
<section class="relative h-[640px] md:h-[769px] overflow-hidden bg-black text-white">
    @if ($banner)
        @if ($banner->tipo_medio === 'video')
            <video src="{{ $banner->foto }}" autoplay muted loop playsinline class="absolute inset-0 size-full object-cover"></video>
        @else
            <img src="{{ $banner->foto }}" alt="" class="absolute inset-0 size-full object-cover">
        @endif
    @endif
    <div class="absolute inset-0"
         style="background-image: linear-gradient(90deg, rgba(0,0,0,.1) 0%, rgba(0,0,0,.1) 100%), linear-gradient(90deg, rgba(0,0,0,.15) 0%, rgba(0,0,0,0) 100%), linear-gradient(180deg, rgba(0,0,0,.4) 0%, rgba(0,0,0,0) 100%);"></div>

    <div class="relative mx-auto flex h-full max-w-[1366px] flex-col justify-center px-4 pt-[40px] md:justify-start md:px-[71px] md:pt-[279px]">
        <h1 class="max-w-[746px] text-[40px] leading-[1.2] md:text-[58px]">{{ $banner?->titulo ?: 'Equipos para profesionales.' }}</h1>
        @if ($banner?->texto)
            <p class="mt-[20px] max-w-[548px] text-[16px] leading-[1.5]">{{ $banner->texto }}</p>
        @endif
        <div class="mt-[56px] flex flex-wrap gap-[24px]">
            <a href="{{ $link('productos') }}" class="flex w-[184px] items-center justify-center rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d]">Ver soluciones</a>
            <a href="{{ $link('contacto') }}" class="flex w-[184px] items-center justify-center rounded-[4px] border border-marca bg-white p-[10px] text-[16px] leading-[1.5] text-marca transition hover:bg-marca hover:text-white">Contacto</a>
        </div>
    </div>
</section>

{{-- ===================== SOPORTE / POST VENTA ===================== --}}
<section class="mx-auto grid max-w-[1366px] grid-cols-1 gap-[24px] px-4 pt-[66px] md:px-[71px] lg:grid-cols-2">
    @foreach ([
        ['bg-marca', $home->soporte_etiqueta, $home->soporte_titulo, $home->soporte_texto, $link('soporte')],
        ['bg-black', $home->postventa_etiqueta, $home->postventa_titulo, $home->postventa_texto, $link('post-venta')],
    ] as [$fondo, $etiqueta, $titulo, $texto, $url])
        <a href="{{ $url }}" class="group flex min-h-[400px] flex-col rounded-[6px] {{ $fondo }} px-[24px] pb-[40px] pt-[60px] text-white md:px-[34px] md:pt-[80px]">
            <p class="text-[14px] font-bold uppercase leading-[1.5]">{{ $etiqueta }}</p>
            <h2 class="max-w-[526px] text-[32px] font-semibold leading-[1.2] md:text-[40px]">{{ $titulo }}</h2>
            <p class="mt-[30px] max-w-[552px] text-[16px] leading-[1.5]">{{ $texto }}</p>
            <span class="mt-auto flex items-center gap-[12px] pt-[30px] text-[16px] font-bold leading-[1.5]">
                Más info
                <img src="{{ asset('assets/img/dixter/icon-move-right-white.svg') }}" alt="" width="24" height="24" class="transition-transform group-hover:translate-x-1">
            </span>
        </a>
    @endforeach
</section>

{{-- ===================== FAMILIAS DE PRODUCTOS ===================== --}}
@if ($familias->isNotEmpty())
<section class="mt-[64px] bg-oscuro text-white">
    <div class="mx-auto max-w-[1366px] px-4 py-[40px] md:px-[71px]">
        <p class="text-[14px] font-bold uppercase leading-[1.5] text-white/70">{{ $home->familias_etiqueta }}</p>
        <h2 class="max-w-[900px] text-[36px] font-semibold leading-[1.2] md:text-[48px]">{{ $home->familias_titulo }}</h2>

        <div class="familias mt-[48px] flex flex-col gap-[16px] lg:h-[520px] lg:flex-row" data-familias>
            @foreach ($familias as $i => $familia)
                <a href="{{ $familia->link ?: $link('productos') }}"
                   class="familia group relative block h-[360px] overflow-hidden rounded-[6px] lg:h-full {{ $i === 0 ? 'activa' : '' }}">
                    @if ($familia->foto)
                        <img src="{{ $familia->foto }}" alt="" class="absolute inset-0 size-full object-cover">
                    @endif
                    <div class="familia-velo absolute inset-0 bg-black"></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-black/5 via-black/20 via-45% to-black/[.88]"></div>

                    <div class="absolute inset-x-[24px] bottom-[40px]">
                        <h3 class="familia-titulo text-[30px] font-light leading-[normal]">{{ $familia->t('nombre') }}</h3>
                        <div class="familia-detalle">
                            <div class="mt-[18px] h-px w-full max-w-[468px] bg-white/50"></div>
                            <p class="mt-[14px] max-w-[420px] text-[15px] leading-[22px]">{{ $familia->t('descripcion') }}</p>
                            <p class="mt-[40px] text-[14px] font-semibold">Ver productos →</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== BENEFICIOS ===================== --}}
<section class="bg-[#f5f5f5]">
    <div class="mx-auto flex max-w-[1366px] flex-col gap-[48px] px-4 py-[40px] md:px-[71px] lg:flex-row lg:items-center lg:gap-[80px] lg:pl-[200px] lg:pr-[140px]">
        <div class="flex flex-col gap-[48px]">
            {{-- Técnicos especializados --}}
            <div class="flex items-center gap-[36px]">
                <div class="relative size-[80px] shrink-0" aria-hidden="true">
                    <img src="{{ asset('assets/img/dixter/anim-tecnicos-base.svg') }}" alt="" width="80" height="80" class="absolute inset-0 size-[80px]">
                    <img src="{{ asset('assets/img/dixter/anim-tecnicos-engranaje.svg') }}" alt="" width="30" height="30" class="anim-girar absolute left-[43px] top-[43px] size-[30px]">
                </div>
                <p class="w-[240px] text-[16px] leading-[24px] text-[#1a1a1a]"><b class="font-bold text-marca">{{ $beneficios[0]['destacado'] ?? '' }}</b> {{ $beneficios[0]['texto'] ?? '' }}</p>
            </div>
            {{-- Repuestos originales --}}
            <div class="flex items-center gap-[36px]">
                <div class="relative size-[80px] shrink-0" aria-hidden="true">
                    <img src="{{ asset('assets/img/dixter/anim-repuestos-base.svg') }}" alt="" width="80" height="80" class="absolute inset-0 size-[80px]">
                    <img src="{{ asset('assets/img/dixter/anim-repuestos-engranaje-grande.svg') }}" alt="" width="38" height="38" class="anim-girar-lento absolute left-[12px] top-[28px] size-[38px]">
                    <img src="{{ asset('assets/img/dixter/anim-repuestos-engranaje-chico.svg') }}" alt="" width="28" height="28" class="anim-girar-inverso absolute left-[41px] top-[10px] size-[28px]">
                </div>
                <p class="w-[240px] text-[16px] leading-[24px] text-[#1a1a1a]"><b class="font-bold text-marca">{{ $beneficios[1]['destacado'] ?? '' }}</b> {{ $beneficios[1]['texto'] ?? '' }}</p>
            </div>
            {{-- Respuesta en 48 hs --}}
            <div class="flex items-center gap-[36px]">
                <div class="relative size-[80px] shrink-0" aria-hidden="true">
                    <img src="{{ asset('assets/img/dixter/anim-respuesta-camion.svg') }}" alt="" width="80" height="80" class="absolute inset-0 size-[80px]">
                    <img src="{{ asset('assets/img/dixter/anim-respuesta-velocidad.svg') }}" alt="" width="20" height="22" class="anim-velocidad absolute left-[4px] top-[28px] h-[22px] w-[20px]">
                </div>
                <p class="w-[240px] text-[16px] leading-[24px] text-[#1a1a1a]"><b class="font-bold text-marca">{{ $beneficios[2]['destacado'] ?? '' }}</b> {{ $beneficios[2]['texto'] ?? '' }}</p>
            </div>
        </div>

        <p class="flex-1 text-[32px] font-light leading-[1.27] text-[#1a1a1a] md:text-[44px]">
            {{ $home->lema }} <span class="font-semibold">{{ $home->lema_destacado }}</span>
        </p>
    </div>
</section>

{{-- ===================== NOSOTROS ===================== --}}
@if ($nosotros)
<section class="mx-auto grid max-w-[1366px] grid-cols-1 gap-[40px] px-4 py-[70px] md:px-[71px] lg:grid-cols-[600px_600px] lg:justify-between lg:gap-[24px]">
    <div class="flex flex-col">
        <p class="text-[14px] font-bold uppercase leading-[1.5]">Nosotros</p>
        <h2 class="max-w-[480px] text-[32px] font-semibold leading-[1.2] md:text-[40px]">{{ $nosotros->texto1 }}</h2>
        <div class="nosotros-texto mt-[59px] text-[18px] leading-[1.5]">{!! $nosotros->descripcion !!}</div>
        <a href="{{ $link('nosotros') }}" class="mt-[32px] flex w-[184px] items-center justify-center rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d]">Más información</a>
    </div>
    @if ($nosotros->foto)
        <img src="{{ $nosotros->foto }}" alt="" class="aspect-square w-full rounded-[8px] object-cover">
    @endif
</section>
@endif

{{-- ===================== INDUSTRIAS ===================== --}}
@if ($sectores->isNotEmpty())
<section class="bg-[#f5f5f5] py-[60px]">
    <div class="mx-auto flex max-w-[1366px] flex-col gap-[24px] px-4 md:flex-row md:items-end md:justify-between md:px-[71px]">
        <div>
            <p class="text-[14px] font-bold uppercase leading-[1.5]">{{ $home->sectores_etiqueta }}</p>
            <h2 class="max-w-[658px] text-[36px] font-semibold leading-[1.2] md:text-[48px]">{{ $home->sectores_titulo }}</h2>
        </div>
        <a href="{{ $link('aplicaciones') }}" class="flex w-[180px] shrink-0 items-center justify-center rounded-[4px] border border-marca bg-white p-[10px] text-[16px] leading-[1.5] text-marca transition hover:bg-marca hover:text-white md:mb-[11px]">Ver todas</a>
    </div>

    <div class="mx-auto mt-[24px] max-w-[1366px] md:mt-[34px]">
        <div class="flex snap-x scroll-pl-4 gap-[24px] overflow-x-auto px-4 pb-4 md:scroll-pl-[70px] md:px-[70px]">
            @foreach ($sectores as $sector)
                <a href="{{ $sector->link ?: $link('aplicaciones') }}"
                   class="group flex h-[317px] w-[288px] shrink-0 snap-start flex-col rounded-[4px] border border-linea bg-white px-[21px] pb-[35px] pt-[49px] transition hover:shadow-[0_0_10px_rgba(0,0,0,0.12)]">
                    @if ($sector->icono)
                        <img src="{{ $sector->icono }}" alt="" width="30" height="30" class="size-[30px]">
                    @endif
                    <h3 class="mt-[24px] text-[20px] font-semibold leading-[1.2]">{{ $sector->t('nombre') }}</h3>
                    <p class="mt-[19px] text-[14px] leading-[1.5]">{{ $sector->t('descripcion') }}</p>
                    <span class="mt-auto flex items-center gap-[8px] text-[16px] font-semibold leading-[1.2]">
                        Ver equipos recomendados
                        <img src="{{ asset('assets/img/dixter/icon-move-right.svg') }}" alt="" width="24" height="24" class="transition-transform group-hover:translate-x-1">
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

@push('styles')
<style>
    /* Familias: acordeón horizontal (Figma: Familias de productos, la tarjeta activa se expande) */
    .familia { transition: flex-grow .5s ease; }
    .familia-velo { opacity: 0; transition: opacity .4s ease; }
    .familia-detalle { transition: opacity .35s ease; }
    @media (min-width: 1024px) {
        .familia { flex: 1 1 0; min-width: 0; }
        .familia.activa { flex-grow: 2.35; }        /* 516px vs 220px en el diseño */
        .familia:not(.activa) .familia-velo { opacity: .35; }
        .familia:not(.activa) .familia-detalle { opacity: 0; }
        .familia .familia-titulo { max-width: 172px; }
        .familia.activa .familia-titulo { max-width: 468px; }
        .familia .familia-detalle { min-width: 420px; }
    }

    /* Íconos animados (Figma: get_motion_context, ciclo de 15 s) */
    @keyframes dxGirar { to { transform: rotate(360deg); } }
    @keyframes dxGirarInverso { to { transform: rotate(-360deg); } }
    @keyframes dxVelocidad {
        0%   { transform: translateX(0); opacity: 1; animation-timing-function: ease-in; }
        94%  { transform: translateX(-12px); opacity: 0; }
        100% { transform: translateX(-12px); opacity: 0; }
    }
    .anim-girar         { animation: dxGirar 3.7s linear infinite; }          /* engranaje técnicos: 4 vueltas en 15 s */
    .anim-girar-lento   { animation: dxGirar 5s linear infinite; }            /* engranaje grande: 3 vueltas */
    .anim-girar-inverso { animation: dxGirarInverso 3s linear infinite; }     /* engranaje chico: 5 vueltas al revés */
    .anim-velocidad     { animation: dxVelocidad .9s infinite; }              /* líneas de velocidad del camión */
    @media (prefers-reduced-motion: reduce) {
        .anim-girar, .anim-girar-lento, .anim-girar-inverso, .anim-velocidad { animation: none; }
        .familia { transition: none; }
    }

    .nosotros-texto p + p { margin-top: 1.5em; }
</style>
@endpush

@push('scripts')
<script>
    // Familias: la tarjeta bajo el mouse (o con foco) pasa a ser la activa
    document.querySelectorAll('[data-familias]').forEach((contenedor) => {
        const tarjetas = contenedor.querySelectorAll('.familia');
        const activar = (t) => tarjetas.forEach(x => x.classList.toggle('activa', x === t));
        tarjetas.forEach((t) => {
            t.addEventListener('mouseenter', () => activar(t));
            t.addEventListener('focus', () => activar(t));
        });
    });
</script>
@endpush
