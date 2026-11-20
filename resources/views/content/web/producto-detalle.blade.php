@extends('layouts.web')

@section('title', $producto->nombre . ' · Dixter')

@use('App\Models\Producto')

@php
    $galeria = $producto->galeria();
    $esFavorito = in_array($producto->id, $favoritos);
    $informacion = array_values(array_filter($producto->informacion ?? [], fn ($f) => filled($f['nombre'] ?? null)));
    $instructivos = array_values(array_filter($producto->instructivos ?? [], fn ($f) => filled($f['archivo'] ?? null)));
    $videos = array_values(array_filter($producto->videos_web ?? [], fn ($f) => filled($f['archivo'] ?? null)));

    // Pestañas con contenido (las vacías no se muestran)
    $pestanas = array_filter([
        'modelos' => $modelos->isNotEmpty() ? 'Modelos' : null,
        'informacion' => count($informacion) ? 'Información adicional' : null,
        'instructivos' => count($instructivos) ? 'Instructivos' : null,
        'videos' => count($videos) ? 'Videos' : null,
    ]);

    $contacto = App\Models\Contacto::first();
    $consultarUrl = $contacto?->whatssap
        ? 'https://wa.me/' . preg_replace('/\D+/', '', $contacto->whatssap) . '?text=' . rawurlencode('Hola, quiero consultar por ' . $producto->nombre . ($producto->codigo ? ' (código ' . $producto->codigo . ')' : ''))
        : (Route::has('contacto') ? route('contacto') : '#');
@endphp

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[100px] md:px-[71px]">
    {{-- Migas --}}
    <p class="pt-[24px] text-[12px] leading-[1.5]">
        <a href="{{ route('home') }}" class="font-bold">Inicio</a> &gt;
        <a href="{{ route('productos') }}" class="font-bold">Productos</a>
        @foreach ($migas as $miga)
            &gt; <a href="{{ route('productos', ['categoria' => $miga->id]) }}" class="font-bold">{{ $miga->nombre }}</a>
        @endforeach
        &gt; {{ $producto->nombre }}
    </p>

    <div class="mt-[40px] grid grid-cols-1 gap-[24px] lg:mt-[76px] lg:grid-cols-[496px_1fr]">
        {{-- ===== Galería ===== --}}
        <div>
            <div class="aspect-square overflow-hidden rounded-[4px] border border-linea bg-white">
                @if ($galeria)
                    <img id="foto-principal" src="{{ $galeria[0] }}" alt="{{ $producto->nombre }}" class="size-full object-contain">
                @endif
            </div>
            @if (count($galeria) > 1)
                <div class="mt-[21px] grid max-w-[471px] grid-cols-5 gap-[10px]">
                    @foreach ($galeria as $i => $foto)
                        <button type="button" data-miniatura="{{ $foto }}" aria-label="Ver foto {{ $i + 1 }}"
                                class="miniatura h-[80px] overflow-hidden rounded-[3px] bg-white {{ $i === 0 ? 'border-2 border-marca' : 'border border-linea' }}">
                            <img src="{{ $foto }}" alt="" class="size-full object-contain">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== Datos ===== --}}
        <div class="flex flex-col">
            <div class="flex items-start justify-between gap-4">
                <p class="text-[20px] font-bold leading-[1.5] text-marca">@if ($producto->codigo)Código: {{ $producto->codigo }}@endif</p>
                <button type="button" data-favorito="{{ $producto->id }}" aria-pressed="{{ $esFavorito ? 'true' : 'false' }}"
                        aria-label="{{ $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' }}"
                        class="favorito mt-[3px] size-[24px] shrink-0 transition hover:scale-110 {{ $esFavorito ? 'activo' : '' }}">
                    @include('components.icono-corazon')
                </button>
            </div>
            <h1 class="text-[28px] font-semibold leading-[1.2] md:text-[36px]">{{ $producto->nombre }}</h1>

            <div class="mt-[16px] border-t border-linea pt-[18px]">
                @if ($producto->descripcion)
                    <div class="descripcion-producto text-[16px] leading-[1.5]">{!! $producto->descripcion !!}</div>
                @endif
            </div>

            {{-- Precio --}}
            <div class="mt-auto pt-[24px] text-right">
                @if ($producto->esConsultar())
                    <p class="text-[36px] font-bold leading-[1.2] md:text-[40px]">Consultar</p>
                @else
                    @if ($producto->enOferta())
                        <p class="text-[18px] leading-[1.2] text-black/50 line-through">{{ Producto::formatearPrecio($producto->precio_anterior) }}</p>
                    @endif
                    <p class="text-[36px] font-bold leading-[1.2] md:text-[40px]">{{ Producto::formatearPrecio($producto->precio) }}</p>
                    @if ($producto->tieneIva())
                        <p class="mt-[4px] text-[16px] leading-[1.5] text-[#17181b]/60">{{ Producto::formatearPrecio($producto->precioSinIva()) }} sin IVA</p>
                    @endif
                @endif
                @if ($info?->cuotas_texto)
                    <p class="ml-auto mt-[4px] max-w-[671px] whitespace-pre-line text-[14px] leading-[1.5] text-[#969696]">{{ $info->cuotas_texto }}</p>
                @endif
            </div>

            {{-- Cantidad + agregar --}}
            <div class="mt-[16px] border-t border-linea pt-[18px]">
                @if ($producto->esConsultar())
                    <a href="{{ $consultarUrl }}" target="_blank" rel="noopener"
                       class="ml-auto flex w-full items-center justify-center rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d] md:w-[392px]">
                        Consultar por este producto
                    </a>
                @else
                    <form data-agregar-carrito class="flex flex-col gap-[16px] md:flex-row md:items-center">
                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                        <label for="cantidad-producto" class="text-[16px] leading-[1.5]">Cantidad</label>
                        <div class="cantidad flex h-[44px] w-[184px] items-center justify-between rounded-l-[4px] border border-linea bg-white px-[16px] md:ml-auto">
                            <button type="button" data-restar aria-label="Restar uno"><img src="{{ asset('assets/img/dixter/icon-minus.svg') }}" alt="" width="16" height="16"></button>
                            <input id="cantidad-producto" name="cantidad" type="number" min="1" max="99" value="1" inputmode="numeric"
                                   class="w-[50px] bg-transparent text-center text-[20px] leading-[1.5] text-[#17181b] [appearance:textfield] focus:outline-none [&::-webkit-inner-spin-button]:appearance-none">
                            <button type="button" data-sumar aria-label="Sumar uno"><img src="{{ asset('assets/img/dixter/icon-plus.svg') }}" alt="" width="16" height="16"></button>
                        </div>
                        <button type="submit" class="flex w-full items-center justify-center gap-[10px] rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d] disabled:opacity-60 md:w-[392px]">
                            <img src="{{ asset('assets/img/dixter/icon-cart-btn.svg') }}" alt="" width="16" height="16">
                            Agregar al carrito
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Pestañas ===== --}}
    @if ($pestanas)
        @php $primera = array_key_first($pestanas); @endphp
        <section class="mt-[47px]" data-pestanas>
            <div class="flex gap-[32px] overflow-x-auto border-b border-linea md:gap-[53px]" role="tablist">
                @foreach ($pestanas as $clave => $titulo)
                    <button type="button" role="tab" id="tab-{{ $clave }}" aria-controls="panel-{{ $clave }}" aria-selected="{{ $clave === $primera ? 'true' : 'false' }}"
                            data-pestana="{{ $clave }}"
                            class="pestana relative shrink-0 whitespace-nowrap pb-[15px] text-[22px] leading-[1.2] md:text-[28px] {{ $clave === $primera ? 'activa font-bold' : '' }}">
                        {{ $titulo }}
                    </button>
                @endforeach
            </div>

            {{-- Modelos --}}
            @if (isset($pestanas['modelos']))
                <div id="panel-modelos" role="tabpanel" aria-labelledby="tab-modelos" class="panel mt-[18px] overflow-x-auto {{ $primera === 'modelos' ? '' : 'hidden' }}">
                    <table class="w-full min-w-[640px] border-collapse text-[16px]">
                        <thead>
                            <tr class="bg-black text-white">
                                <th class="w-[27%] px-[17px] text-left text-[20px] font-bold leading-[40px]">Código</th>
                                @foreach ($modelos as $modelo)
                                    <th class="text-center text-[20px] font-bold leading-[40px]">{{ $modelo->codigo ?: '—' }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filasModelos as $i => $fila)
                                <tr class="{{ $i % 2 === 0 ? 'bg-[#f5f5f5]' : 'bg-white' }} border-b border-linea">
                                    <th scope="row" class="bg-marca/95 px-[17px] text-left font-bold leading-[40px] text-white">{{ $fila }}</th>
                                    @foreach ($modelos as $modelo)
                                        <td class="text-center leading-[40px]">{{ collect($modelo->ficha)->firstWhere('nombre', $fila)['valor'] ?? '—' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            @if ($modelos->count() > 1)
                                <tr class="{{ count($filasModelos) % 2 === 0 ? 'bg-[#f5f5f5]' : 'bg-white' }}">
                                    <th scope="row" class="bg-marca/95 px-[17px] text-left font-bold leading-[40px] text-white">Ver modelo</th>
                                    @foreach ($modelos as $modelo)
                                        <td class="px-2 py-[4px] text-center">
                                            @unless ($modelo->is($producto))
                                                <a href="{{ route('productos.detalle', $modelo) }}" class="inline-flex h-[33px] w-full max-w-[142px] items-center justify-center rounded-[4px] border border-marca bg-white text-marca transition hover:bg-marca hover:text-white">Ver modelo</a>
                                            @endunless
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Información adicional --}}
            @if (isset($pestanas['informacion']))
                <div id="panel-informacion" role="tabpanel" aria-labelledby="tab-informacion" class="panel mt-[30px] grid grid-cols-1 gap-x-[24px] md:grid-cols-2 {{ $primera === 'informacion' ? '' : 'hidden' }}">
                    @foreach ($informacion as $dato)
                        <div class="flex items-center justify-between border-y border-linea text-[16px] leading-[50px] -mt-px">
                            <span>{{ $dato['nombre'] }}</span>
                            <span class="font-bold text-right">{{ $dato['valor'] ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Instructivos --}}
            @if (isset($pestanas['instructivos']))
                <ul id="panel-instructivos" role="tabpanel" aria-labelledby="tab-instructivos" class="panel mt-[30px] border-t border-linea {{ $primera === 'instructivos' ? '' : 'hidden' }}">
                    @foreach ($instructivos as $doc)
                        <li class="flex min-h-[60px] items-center justify-between gap-4 border-b border-linea py-[8px]">
                            <span class="text-[16px] font-bold">{{ $doc['titulo'] ?: basename($doc['archivo']) }}</span>
                            <a href="{{ $doc['archivo'] }}" target="_blank" rel="noopener" download class="flex shrink-0 items-center gap-[10px] text-[16px] font-bold hover:text-marca">
                                Descargar <img src="{{ asset('assets/img/dixter/icon-download.svg') }}" alt="" width="24" height="24">
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- Videos --}}
            @if (isset($pestanas['videos']))
                <div id="panel-videos" role="tabpanel" aria-labelledby="tab-videos" class="panel mt-[30px] grid grid-cols-1 gap-[24px] md:grid-cols-2 {{ $primera === 'videos' ? '' : 'hidden' }}">
                    @foreach ($videos as $video)
                        <figure>
                            <video src="{{ $video['archivo'] }}#t=0.5" controls preload="metadata" playsinline
                                   class="aspect-video w-full rounded-[4px] bg-black"
                                   @if (!empty($video['titulo'])) aria-label="{{ $video['titulo'] }}" @endif></video>
                            @if (!empty($video['titulo']))<figcaption class="mt-[8px] text-[16px]">{{ $video['titulo'] }}</figcaption>@endif
                        </figure>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- ===== Relacionados ===== --}}
    @if ($relacionados->isNotEmpty())
        <section class="mt-[70px]">
            <h2 class="text-[28px] font-semibold leading-[1.2] md:text-[32px]">Productos relacionados</h2>
            <div class="mt-[24px] grid grid-cols-1 gap-[24px] sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($relacionados as $relacionado)
                    @include('components.producto-card', ['producto' => $relacionado])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

@push('styles')
<style>
    .descripcion-producto p { margin: 0; }
    .descripcion-producto p + p, .descripcion-producto ul + p { margin-top: 14px; }
    /* subtítulo en negrita pegado a su texto, como en el Figma */
    .descripcion-producto p:has(> strong:only-child) + p,
    .descripcion-producto p:has(> strong:only-child) + ul { margin-top: 0; }
    .descripcion-producto strong, .descripcion-producto b, .descripcion-producto h3, .descripcion-producto h4 { font-weight: 700; font-size: 16px; }
    .descripcion-producto ul { padding-left: 20px; list-style: none; }
    .descripcion-producto ul li { position: relative; }
    .descripcion-producto ul li::before { content: ""; position: absolute; left: -14px; top: .65em; width: 6px; height: 6px; border-radius: 50%; background: #C32922; }
    .pestana.activa::after { content: ""; position: absolute; left: 0; right: 0; bottom: -1px; height: 3px; background: #C32922; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Galería
    const principal = document.getElementById('foto-principal');
    document.querySelectorAll('[data-miniatura]').forEach((boton) => {
        boton.addEventListener('click', () => {
            principal.src = boton.dataset.miniatura;
            document.querySelectorAll('.miniatura').forEach((m) => {
                m.classList.toggle('border-2', m === boton);
                m.classList.toggle('border-marca', m === boton);
                m.classList.toggle('border', m !== boton);
                m.classList.toggle('border-linea', m !== boton);
            });
        });
    });

    // Pestañas
    document.querySelectorAll('[data-pestanas]').forEach((seccion) => {
        seccion.querySelectorAll('[data-pestana]').forEach((tab) => {
            tab.addEventListener('click', () => {
                seccion.querySelectorAll('[data-pestana]').forEach((t) => {
                    const activa = t === tab;
                    t.classList.toggle('activa', activa);
                    t.classList.toggle('font-bold', activa);
                    t.setAttribute('aria-selected', activa);
                    document.getElementById('panel-' + t.dataset.pestana).classList.toggle('hidden', !activa);
                });
            });
        });
    });
});
</script>
@endpush
