@extends('layouts.web', ['header' => 'clientes'])

@section('title', 'Mis productos · Área clientes Dixter')

@php
    $videos = $producto?->videosCargados() ?? [];
    $documentos = $producto?->documentosCargados() ?? [];
@endphp

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[120px] md:px-[71px]">
    {{-- Migas --}}
    <p class="pt-[24px] text-[12px] leading-[1.5]"><a href="{{ route('home') }}" class="font-bold">Inicio</a> &gt; Mis productos</p>

    <h1 class="mt-[60px] text-[36px] font-semibold leading-[1.2] md:mt-[83px] md:text-[48px]">Mis productos</h1>

    @if ($productos->isNotEmpty())
        <div class="mt-[40px] grid grid-cols-1 items-start gap-[24px] lg:grid-cols-[288px_1fr] md:mt-[46px]">

            {{-- Listado de productos del cliente --}}
            <aside class="rounded-[6px] border border-linea px-[16px] pb-[14px] pt-[14px]">
                <button type="button" id="toggle-mis-productos" aria-expanded="true" aria-controls="lista-mis-productos"
                        class="flex w-full items-center justify-between border-b border-linea pb-[10px] text-left">
                    <span class="text-[20px] font-bold leading-[1.5]">Mis productos</span>
                    <img src="{{ asset('assets/img/dixter/icon-chevron-down.svg') }}" alt="" width="18" height="18" class="chevron rotate-180 transition-transform">
                </button>
                <ul id="lista-mis-productos" class="pt-[8px]">
                    @foreach ($productos as $item)
                        <li>
                            <a href="{{ route('cliente.productos', $item->id) }}"
                               class="block py-[4px] text-[16px] leading-[1.6] hover:text-marca {{ $producto && $item->id === $producto->id ? 'font-bold' : '' }}"
                               @if ($producto && $item->id === $producto->id) aria-current="page" @endif>
                                {{ $item->nombre }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            {{-- Material del producto --}}
            <div class="min-w-0">
                {{-- Advertencia (Figma: recuadro gris centrado arriba de los videos) --}}
                @if ($producto->advertencia)
                    <div class="mb-[40px] flex min-h-[85px] items-center justify-center rounded-[4px] border border-linea bg-gris/5 px-[24px] py-[14px] text-center text-[16px] leading-[1.5]">
                        <p class="max-w-[671px]"><b class="font-bold">Advertencia.</b> {{ $producto->advertencia }}</p>
                    </div>
                @endif

                {{-- Videos --}}
                @if (count($videos))
                    <h2 class="text-[28px] font-semibold leading-[1.2] md:text-[32px]">Videos</h2>
                    <div class="mt-[23px] grid grid-cols-1 gap-[24px] sm:grid-cols-2">
                        @foreach ($videos as $video)
                            <button type="button" data-video="{{ $video['archivo'] }}" class="group block text-left">
                                <span class="relative block aspect-[444/226] overflow-hidden rounded-[4px] bg-black">
                                    {{-- El primer cuadro del video hace de miniatura --}}
                                    <video src="{{ $video['archivo'] }}#t=0.5" preload="metadata" muted playsinline tabindex="-1"
                                           class="pointer-events-none absolute inset-0 size-full object-cover transition-transform duration-500 group-hover:scale-105"></video>
                                    <span class="absolute inset-0 bg-black/10 transition group-hover:bg-black/25"></span>
                                    <img src="{{ asset('assets/img/dixter/icon-square-play.svg') }}" alt="" width="24" height="24"
                                         class="absolute left-1/2 top-1/2 size-[24px] -translate-x-1/2 -translate-y-1/2 transition-transform group-hover:scale-125">
                                </span>
                                <span class="mt-[8px] block text-[16px] leading-[1.5]">{{ $video['titulo'] ?: 'Video' }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Manuales y documentos --}}
                @if (count($documentos))
                    <h2 class="{{ count($videos) ? 'mt-[50px]' : '' }} text-[28px] font-semibold leading-[1.2] md:text-[32px]">Manuales y documentos</h2>
                    <ul class="mt-[30px] border-t border-linea">
                        @foreach ($documentos as $documento)
                            <li class="flex min-h-[60px] items-center justify-between gap-4 border-b border-linea py-[8px]">
                                <span class="text-[16px] font-bold leading-[1.5]">{{ $documento['titulo'] ?: basename($documento['archivo']) }}</span>
                                <a href="{{ $documento['archivo'] }}" target="_blank" rel="noopener" download
                                   class="flex shrink-0 items-center gap-[10px] py-[10px] pr-[11px] text-[16px] font-bold leading-[1.5] hover:text-marca">
                                    Descargar
                                    <img src="{{ asset('assets/img/dixter/icon-download.svg') }}" alt="" width="24" height="24">
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if (!$producto->advertencia && !count($videos) && !count($documentos))
                    <p class="rounded-[4px] border border-linea bg-gris/5 px-[24px] py-[28px] text-[16px] leading-[1.5]">
                        Pronto vas a encontrar acá los videos y manuales de este equipo.
                    </p>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- Reproductor de los videos subidos --}}
<div id="modal-video" class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/80 px-4" role="dialog" aria-modal="true" aria-label="Video">
    <div class="relative w-full max-w-[960px]">
        <button type="button" data-cerrar-video class="absolute -top-[40px] right-0 text-[32px] leading-none text-white" aria-label="Cerrar">&times;</button>
        <video id="reproductor-video" class="aspect-video w-full rounded-[4px] bg-black" controls playsinline></video>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Plegar / desplegar el listado (chevron del diseño)
    const toggle = document.getElementById('toggle-mis-productos');
    toggle?.addEventListener('click', () => {
        const lista = document.getElementById('lista-mis-productos');
        const abierto = lista.classList.toggle('hidden') === false;
        toggle.setAttribute('aria-expanded', abierto);
        toggle.querySelector('.chevron').classList.toggle('rotate-180', abierto);
    });

    // Videos subidos: se reproducen en un modal
    const modal = document.getElementById('modal-video');
    const reproductor = document.getElementById('reproductor-video');
    const cerrar = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        reproductor.pause();
        reproductor.removeAttribute('src');
        reproductor.load();
    };

    document.querySelectorAll('[data-video]').forEach((boton) => {
        boton.addEventListener('click', () => {
            reproductor.src = boton.dataset.video;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            reproductor.play().catch(() => {});
        });
    });
    modal.addEventListener('click', (e) => { if (e.target === modal || e.target.closest('[data-cerrar-video]')) cerrar(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) cerrar(); });
});
</script>
@endpush
