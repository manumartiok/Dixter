@extends('layouts.web')

@section('title', 'Nosotros · Dixter')

@php
    // Centro de cada ítem dentro del diagrama de 1224x660 (Figma: Nuestro compromiso), en el orden del admin
    $posiciones = [[329, 397], [895, 397], [613, 202], [421, 572], [805, 572]];

    // "¿Por qué elegirnos?": logos 1 a 4 del admin (Nosotros > Contenido)
    $logos = collect([1, 2, 3, 4])
        ->map(fn ($n) => [
            'icono' => $nosotros?->{"icono$n"},
            'titulo' => $nosotros?->{"nombre_icono$n"},
            'texto' => $nosotros?->{"texto_icono$n"},
        ])
        ->filter(fn ($l) => $l['titulo'] || $l['texto']);
@endphp

@section('content')
<div class="mx-auto max-w-[1366px] px-4 md:px-[71px]">
    @include('components.encabezado-pagina', [
        'migas' => [['Nosotros', null]],
        'etiqueta' => 'Nosotros',
        'titulo' => $nosotros?->texto1 ?: 'Desde 1947, fabricando en Argentina.',
    ])

    <div class="mt-[40px] grid grid-cols-1 gap-[40px] pb-[80px] lg:mt-[25px] lg:grid-cols-2 lg:gap-[24px]">
        <div class="nosotros-texto text-[16px] leading-[1.5] md:text-[18px] lg:pt-[33px]">{!! $nosotros?->descripcion !!}</div>
        @if ($nosotros?->foto)
            <img src="{{ $nosotros->foto }}" alt="Fachada de Dixter" class="aspect-square w-full rounded-[8px] object-cover">
        @endif
    </div>
</div>

{{-- Pilares + compromiso: el gris termina a mitad del diagrama, como en el Figma --}}
<section class="bg-[linear-gradient(#f5f5f5_calc(100%-490px),#fff_calc(100%-490px))] pb-[80px] pt-[60px]">
    <div class="mx-auto max-w-[1366px] px-4 md:px-[71px]">
        @if ($logos->isNotEmpty())
            <p class="text-[14px] font-bold uppercase leading-[1.5]">Nuestros pilares</p>
            <h2 class="text-[34px] font-semibold leading-[1.2] md:text-[48px]">{{ $nosotros?->texto2 ?: '¿Por qué elegirnos?' }}</h2>

            <div class="mt-[24px] grid grid-cols-1 gap-[24px] sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($logos as $logo)
                    <div class="flex min-h-[288px] flex-col rounded-[4px] border border-linea bg-white px-[21px] pb-[30px] pt-[55px]">
                        @if ($logo['icono'])<img src="{{ $logo['icono'] }}" alt="" width="30" height="30" class="size-[30px] object-contain">@endif
                        <h3 class="mt-[24px] text-[20px] font-semibold leading-[1.2]">{{ $logo['titulo'] }}</h3>
                        <div class="logo-texto mt-[18px] text-[14px] leading-[1.5]">{!! $logo['texto'] !!}</div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($compromisos->isNotEmpty())
            <div class="compromiso relative mt-[60px] overflow-hidden rounded-[6px] px-4 py-[40px] lg:aspect-[1224/660] lg:p-0"
                 style="background-image: linear-gradient(90deg, rgba(195,41,34,.1), rgba(195,41,34,.1)), linear-gradient(180deg, #f6f7fa 0%, #edf0f4 100%);"
                 data-compromiso>
              {{-- En desktop todo se dibuja en una escena de 1224x660 (medidas del Figma) que se escala al ancho disponible --}}
              <div class="compromiso-escena">
                {{-- Red de líneas y órbita (decorativo) --}}
                <div class="pointer-events-none absolute inset-0 hidden lg:block" aria-hidden="true">
                    <img src="{{ asset('assets/img/dixter/compromiso-red-1.svg') }}" alt="" class="absolute left-[1.14%] top-[0.61%] h-[97.12%] w-[96.57%]">
                    <img src="{{ asset('assets/img/dixter/compromiso-red-2.svg') }}" alt="" class="absolute left-[1.02%] top-[0.27%] h-[97.91%] w-[96.81%]">
                    <img src="{{ asset('assets/img/dixter/compromiso-orbita.png') }}" alt="" width="640" height="400" class="absolute left-[292px] top-[200px] h-[400px] w-[640px]">
                    <img src="{{ asset('assets/img/dixter/compromiso-halo.svg') }}" alt="" class="compromiso-halo absolute left-[432px] top-[220px] size-[360px]">
                    <div class="absolute left-[487px] top-[362px] h-[73px] w-[250px] rounded-[10px] bg-white"></div>
                    <img src="{{ asset('assets/img/dixter/logo.png') }}" alt="" width="226" height="35" class="absolute left-[499px] top-[383px] h-[35px] w-[226px] object-cover">
                </div>

                <h2 class="relative text-center text-[34px] font-light leading-[normal] text-[#1a1a1f] md:text-[48px] lg:pt-[48px]">Nuestro compromiso</h2>

                <ul class="relative mt-[24px] flex flex-col gap-[12px] lg:absolute lg:inset-0 lg:mt-0 lg:block">
                    @foreach ($compromisos as $i => $item)
                        @php [$x, $y] = $posiciones[$i]; @endphp
                        <li class="compromiso-item {{ $i === 0 ? 'activo' : '' }}" style="--x: {{ $x }}px; --y: {{ $y }}px" data-item="{{ $i }}" tabindex="0">
                            @if ($item->icono)<img src="{{ $item->icono }}" alt="" class="compromiso-icono shrink-0">@endif
                            <div class="min-w-0">
                                <p class="compromiso-titulo">{{ $item->t('titulo') }}</p>
                                <p class="compromiso-texto">{{ $item->t('texto') }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
              </div>
            </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
    .nosotros-texto p + p, .logo-texto p + p { margin-top: 1em; }

    /* Ítems del compromiso: en mobile, lista; en desktop, ubicados por su centro sobre el diagrama */
    .compromiso-item {
        display: flex; align-items: center; gap: 14px;
        background: #fff; border: 1px solid #e0e3eb; border-radius: 16px;
        padding: 16px 18px; box-shadow: 0 16px 36px rgba(26, 31, 51, .16);
        transition: all .45s cubic-bezier(.4, 0, .2, 1); cursor: pointer;
    }
    .compromiso-icono { width: 32px; height: 32px; transition: all .45s; }
    .compromiso-titulo { font-size: 19px; font-weight: 600; color: #1a1a1f; white-space: nowrap; transition: all .45s; }
    .compromiso-texto { margin-top: 4px; width: 240px; max-width: 100%; font-size: 14px; line-height: 20px; color: #6b707a; }

    @media (min-width: 1024px) {
        .compromiso-escena {
            position: absolute; left: 0; top: 0; width: 1224px; height: 660px;
            transform: scale(var(--escala, 1)); transform-origin: 0 0;
        }
        .compromiso-item {
            position: absolute; left: var(--x); top: var(--y); transform: translate(-50%, -50%);
            z-index: 1;
        }
        .compromiso-item:not(.activo) {
            gap: 10px; width: 232px; padding: 12px 16px; border-radius: 10px;
            background: rgba(255, 255, 255, .65); opacity: .7; box-shadow: 0 4px 12px rgba(26, 31, 51, .05);
        }
        .compromiso-item:not(.activo) .compromiso-icono { width: 24px; height: 24px; filter: grayscale(1) brightness(1.15); }
        .compromiso-item:not(.activo) .compromiso-titulo { font-size: 17px; font-weight: 400; color: #6b707a; }
        .compromiso-item:not(.activo) .compromiso-texto { display: none; }
        .compromiso-item.activo { z-index: 2; animation: compromisoEntrar .45s ease-out; }
        @keyframes compromisoEntrar { from { opacity: .6; transform: translate(-50%, -50%) scale(.92); } to { opacity: 1; transform: translate(-50%, -50%) scale(1); } }
        .compromiso-halo { animation: compromisoHalo 4s ease-in-out infinite; }
        @keyframes compromisoHalo { 50% { transform: scale(1.06); opacity: .85; } }
    }
    @media (prefers-reduced-motion: reduce) {
        .compromiso-item, .compromiso-halo { animation: none !important; transition: none; }
    }
</style>
@endpush

@push('scripts')
<script>
// Nuestro compromiso: cada 3,5 s se destaca el ítem siguiente (como las 5 variantes del Figma).
// El mouse o el foco sobre un ítem lo destaca y pausa el ciclo.
document.querySelectorAll('[data-compromiso]').forEach((diagrama) => {
    // Escala la escena de 1224px al ancho real del contenedor
    const escalar = () => diagrama.style.setProperty('--escala', diagrama.clientWidth / 1224);
    escalar();
    new ResizeObserver(escalar).observe(diagrama);

    const items = [...diagrama.querySelectorAll('.compromiso-item')];
    if (items.length < 2) return;
    let actual = 0, pausado = false;
    const activar = (i) => { actual = i; items.forEach((el, j) => el.classList.toggle('activo', j === i)); };
    items.forEach((el, i) => {
        el.addEventListener('mouseenter', () => { pausado = true; activar(i); });
        el.addEventListener('focus', () => { pausado = true; activar(i); });
        el.addEventListener('click', () => activar(i));
    });
    diagrama.addEventListener('mouseleave', () => { pausado = false; });
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        setInterval(() => { if (!pausado && window.innerWidth >= 1024) activar((actual + 1) % items.length); }, 3500);
    }
});
</script>
@endpush
