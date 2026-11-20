@extends('layouts.web')

@section('title', 'Nuestra red · Dónde comprar · Dixter')

@use('App\Models\Distribuidore')

@php
    $control = 'h-[44px] w-full rounded-[4px] border border-linea bg-white px-[15px] text-[14px] focus:border-marca focus:outline-none';
    $porPagina = 5;
    $leyenda = ['todas' => Distribuidore::TODAS[0]] + array_map(fn ($l) => $l[0], Distribuidore::LINEAS);
    $marcadores = $distribuidores->filter(fn ($d) => $d->latitud && $d->longitud)->map(fn ($d) => [
        'id' => $d->id,
        'lat' => $d->latitud,
        'lng' => $d->longitud,
        'icono' => asset('assets/img/dixter/marcador-' . $d->claveLinea() . '.svg'),
        'nombre' => $d->nombre,
        'direccion' => trim($d->direccion . ($d->localidad ? ', ' . $d->localidad : '')),
    ])->values();
@endphp

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[100px] md:px-[71px]">
    @include('components.encabezado-pagina', [
        'migas' => [['Donde comprar', null]],
        'etiqueta' => 'Nuestra red',
        'titulo' => $titulo,
    ])

    {{-- Filtros --}}
    <form method="GET" action="{{ route('nuestra-red') }}" class="mt-[48px] grid grid-cols-1 items-end gap-[16px] md:grid-cols-[288px_288px_288px_1fr] md:gap-x-[23px]">
        <div>
            <label for="filtro-provincia" class="text-[16px] leading-[1.5]">Provincia</label>
            <div class="relative mt-[8px]">
                <select id="filtro-provincia" name="provincia" class="{{ $control }} appearance-none pr-[44px]">
                    <option value="">Seleccionar provincia</option>
                    @foreach ($provincias as $provincia)
                        <option value="{{ $provincia }}" @selected(request('provincia') === $provincia)>{{ $provincia }}</option>
                    @endforeach
                </select>
                <img src="{{ asset('assets/img/dixter/icon-chevron-select.svg') }}" alt="" width="24" height="24" class="pointer-events-none absolute right-[16px] top-[10px]">
            </div>
        </div>
        <div>
            <label for="filtro-localidad" class="text-[16px] leading-[1.5]">Ciudad / Localidad</label>
            <input id="filtro-localidad" name="localidad" type="text" value="{{ request('localidad') }}" placeholder="Ciudad / Localidad" class="{{ $control }} mt-[8px] placeholder:text-black/50">
        </div>
        <div>
            <label for="filtro-linea" class="text-[16px] leading-[1.5]">Líneas</label>
            <div class="relative mt-[8px]">
                <select id="filtro-linea" name="linea" class="{{ $control }} appearance-none pr-[44px]">
                    <option value="">Todas las lineas</option>
                    @foreach (Distribuidore::LINEAS as $clave => [$texto])
                        <option value="{{ $clave }}" @selected(request('linea') === $clave)>{{ $texto }}</option>
                    @endforeach
                </select>
                <img src="{{ asset('assets/img/dixter/icon-chevron-select.svg') }}" alt="" width="24" height="24" class="pointer-events-none absolute right-[16px] top-[10px]">
            </div>
        </div>
        <div class="md:flex md:justify-end md:pr-[29px]">
            <button type="submit" class="h-[44px] w-full rounded-[4px] bg-marca text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d] md:w-[181px]">Buscar</button>
        </div>
    </form>

    <div class="mt-[29px] grid grid-cols-1 gap-[24px] lg:grid-cols-[327px_1fr] lg:gap-x-[90px]">
        {{-- Listado --}}
        <div class="flex flex-col">
            @if ($distribuidores->isEmpty())
                <p class="border-y border-[#898888]/40 px-[8px] py-[24px] text-[16px]">No encontramos distribuidores con esos filtros.
                    <a href="{{ route('nuestra-red') }}" class="mt-2 block font-semibold text-marca underline">Ver todos</a></p>
            @else
                <ul id="lista-distribuidores" class="lista-red border-t border-[#898888]/40 lg:max-h-[655px] lg:overflow-y-auto lg:pr-[8px]">
                    @foreach ($distribuidores as $i => $d)
                        <li class="distribuidor border-b border-[#898888]/40 {{ $i >= $porPagina ? 'hidden' : '' }}" data-distribuidor="{{ $d->id }}">
                            <button type="button" class="w-full px-[8px] py-[16px] text-left transition hover:bg-black/[.03]" data-ver-en-mapa="{{ $d->id }}">
                                <span class="block text-[16px] font-bold leading-[1.2]" style="color: {{ $d->colorLinea() }}">{{ $d->nombre }}</span>
                                <span class="mt-[8px] block text-[12px] leading-[1.5] text-[#262424]">
                                    @if ($d->direccion){{ $d->direccion }}{{ $d->localidad ? ', ' . $d->localidad : '' }}{{ $d->provincia ? '. ' . $d->provincia : '' }}<br>@endif
                                    @if ($d->web){{ preg_replace('~^https?://~', '', $d->web) }}<br>@endif
                                    @if ($d->email){{ $d->email }}<br>@endif
                                    @if ($d->telefono){{ $d->telefono }}@endif
                                </span>
                            </button>
                        </li>
                    @endforeach
                </ul>
                @if ($distribuidores->count() > $porPagina)
                    <button type="button" id="cargar-mas" class="mt-[13px] w-full rounded-[4px] border border-marca bg-white p-[10px] text-[16px] leading-[1.5] text-marca transition hover:bg-marca hover:text-white">Cargar más</button>
                @endif
            @endif
        </div>

        {{-- Mapa --}}
        <div class="flex flex-col gap-[16px]">
            <div id="mapa-red" class="h-[420px] w-full overflow-hidden rounded-[4px] bg-[#e8e8e8] md:h-[711px]" aria-label="Mapa de distribuidores"></div>

            <div class="flex flex-wrap items-center gap-x-[14px] gap-y-[4px] rounded-[4px] border border-linea px-[22px] py-[8px] xl:flex-nowrap">
                <p class="text-[16px] font-bold leading-[1.5]">Referencias:</p>
                @foreach ($leyenda as $clave => $texto)
                    <span class="flex items-center gap-[8px] whitespace-nowrap text-[14px] leading-[1.5]">
                        <img src="{{ asset('assets/img/dixter/linea-' . $clave . '.svg') }}" alt="" width="38" height="38" class="size-[38px]">
                        {{ $texto }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<style>
    .lista-red::-webkit-scrollbar { width: 8px; }
    .lista-red::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .lista-red::-webkit-scrollbar-thumb { background: #c4c4c4; border-radius: 4px; }
    .distribuidor.activo { background: rgba(195, 41, 34, .05); }
    #mapa-red .leaflet-tile-pane { filter: grayscale(1) brightness(1.06) contrast(.85); }
    .leaflet-popup-content { font-family: "Nunito Sans", sans-serif; margin: 12px 16px; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const datos = @json($marcadores);
    const mapa = L.map('mapa-red', { scrollWheelZoom: false }).setView([-34.62, -58.45], 10);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(mapa);

    const escapar = (t) => String(t ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const marcadores = {};
    datos.forEach((d) => {
        const icono = L.icon({ iconUrl: d.icono, iconSize: [56, 66], iconAnchor: [28, 66], popupAnchor: [0, -60] });
        marcadores[d.id] = L.marker([d.lat, d.lng], { icon: icono, title: d.nombre })
            .addTo(mapa)
            .bindPopup('<b>' + escapar(d.nombre) + '</b>' + (d.direccion ? '<br>' + escapar(d.direccion) : ''))
            .on('click', () => marcar(d.id));
    });
    if (datos.length) {
        mapa.fitBounds(L.latLngBounds(datos.map((d) => [d.lat, d.lng])), { padding: [60, 60], maxZoom: 13 });
    }

    function marcar(id) {
        document.querySelectorAll('.distribuidor').forEach((li) => li.classList.toggle('activo', li.dataset.distribuidor == id));
    }

    // Clic en el listado: centra el mapa en el distribuidor
    document.querySelectorAll('[data-ver-en-mapa]').forEach((boton) => {
        boton.addEventListener('click', () => {
            const marcador = marcadores[boton.dataset.verEnMapa];
            marcar(boton.dataset.verEnMapa);
            if (!marcador) return;
            mapa.flyTo(marcador.getLatLng(), 14, { duration: .6 });
            marcador.openPopup();
            if (window.innerWidth < 1024) document.getElementById('mapa-red').scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });

    // Cargar más: muestra 5 distribuidores más
    document.getElementById('cargar-mas')?.addEventListener('click', function () {
        const ocultos = [...document.querySelectorAll('.distribuidor.hidden')];
        ocultos.slice(0, {{ $porPagina }}).forEach((li) => li.classList.remove('hidden'));
        if (ocultos.length <= {{ $porPagina }}) this.remove();
    });
});
</script>
@endpush
