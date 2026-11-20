@extends('layouts.web')

@section('title', 'Contacto · Dixter')

@php
    $c = $contacto;
    $input = 'mt-[8px] w-full rounded-[4px] border border-linea bg-white px-[15px] text-[14px] placeholder:text-black/50 focus:border-marca focus:outline-none';
@endphp

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[80px] md:px-[71px]">
    @include('components.encabezado-pagina', [
        'migas' => [['Contacto', null]],
        'etiqueta' => 'Contacto',
        'titulo' => 'Hablemos',
    ])

    <div class="mt-[40px] grid grid-cols-1 gap-[40px] lg:mt-[54px] lg:grid-cols-[318px_1fr] lg:gap-x-[98px]">
        {{-- Datos --}}
        <ul class="flex flex-col gap-[18px] text-[16px] leading-[1.5]">
            @if ($c?->direccion)
                <li class="flex items-start gap-[15px]">
                    <img src="{{ asset('assets/img/dixter/contacto-pin.svg') }}" alt="" width="20" height="20" class="mt-[3px] shrink-0">
                    <a href="https://maps.google.com/?q={{ urlencode(str_replace("\n", ' ', $c->direccion)) }}" target="_blank" rel="noopener" class="hover:text-marca">{{ str_replace("\n", ' ', $c->direccion) }}</a>
                </li>
            @endif
            @if ($c?->telefono)
                <li class="flex items-center gap-[15px]">
                    <img src="{{ asset('assets/img/dixter/contacto-phone.svg') }}" alt="" width="20" height="20" class="shrink-0">
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $c->telefono) }}" class="hover:text-marca">{{ $c->telefono }}</a>
                </li>
            @endif
            @if ($c?->mail)
                <li class="flex items-center gap-[15px]">
                    <img src="{{ asset('assets/img/dixter/contacto-mail.svg') }}" alt="" width="20" height="20" class="shrink-0">
                    <a href="mailto:{{ $c->mail }}" class="hover:text-marca">{{ $c->mail }}</a>
                </li>
            @endif
            @if ($c?->whatssap)
                <li class="flex items-center gap-[15px]">
                    <img src="{{ asset('assets/img/dixter/contacto-whatsapp.svg') }}" alt="" width="20" height="20" class="shrink-0">
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $c->whatssap) }}" target="_blank" rel="noopener" class="hover:text-marca">{{ $c->whatssap }}</a>
                </li>
            @endif
        </ul>

        {{-- Formulario --}}
        <form id="form-contacto" action="{{ route('form.contacto') }}" method="POST" class="grid grid-cols-1 gap-x-[25px] gap-y-[24px] md:grid-cols-2" novalidate>
            @csrf
            <div>
                <label for="contacto-nombre" class="text-[16px] leading-[1.5]">Nombre y Apellido*</label>
                <input id="contacto-nombre" name="nombre" type="text" required autocomplete="name" placeholder="Juan Perez" class="{{ $input }} h-[45px]">
            </div>
            <div>
                <label for="contacto-email" class="text-[16px] leading-[1.5]">E-mail*</label>
                <input id="contacto-email" name="email" type="email" required autocomplete="email" placeholder="juanperez@mail.com" class="{{ $input }} h-[45px]">
            </div>
            <div>
                <label for="contacto-telefono" class="text-[16px] leading-[1.5]">Teléfono</label>
                <input id="contacto-telefono" name="telefono" type="tel" autocomplete="tel" placeholder="011 1234-5678" class="{{ $input }} h-[45px]">
            </div>
            <div>
                <label for="contacto-empresa" class="text-[16px] leading-[1.5]">Empresa</label>
                <input id="contacto-empresa" name="empresa" type="text" autocomplete="organization" placeholder="Nombre de la empresa / Razón social" class="{{ $input }} h-[45px]">
            </div>
            <div>
                <label for="contacto-mensaje" class="text-[16px] leading-[1.5]">Mensaje*</label>
                <textarea id="contacto-mensaje" name="mensaje" rows="5" required placeholder="Dejanos un mensaje" class="{{ $input }} h-[144px] py-[14px]"></textarea>
            </div>
            <div class="flex flex-col justify-end gap-[12px] sm:flex-row sm:items-center sm:justify-between md:self-end">
                <p class="text-[16px] leading-[1.5]">*Datos obligatorios</p>
                <button type="submit" class="w-full rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d] disabled:opacity-60 sm:w-[183px]">Enviar mensaje</button>
            </div>
            <p id="contacto-resultado" class="hidden text-[15px] md:col-span-2" role="status"></p>
        </form>
    </div>
</div>

{{-- Mapa --}}
@if ($c?->latitud && $c?->longitud)
    <div id="mapa-contacto" class="h-[400px] w-full bg-[#e8e8e8] md:h-[574px]" aria-label="Mapa: ubicación de Dixter"></div>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<style>
    #mapa-contacto .leaflet-tile-pane { filter: grayscale(1) brightness(1.06) contrast(.85); }
    .tooltip-dixter { background: transparent; border: 0; box-shadow: none; padding: 0; font: 600 14px/1.2 "Nunito Sans", sans-serif; color: #3c3c3c; text-shadow: 0 0 3px #fff, 0 0 3px #fff; }
    .tooltip-dixter::before { display: none; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const contenedor = document.getElementById('mapa-contacto');
    if (contenedor) {
        const punto = [{{ (float) $c?->latitud }}, {{ (float) $c?->longitud }}];
        const mapa = L.map(contenedor, { scrollWheelZoom: false }).setView(punto, 16);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(mapa);
        L.marker(punto, {
            icon: L.icon({ iconUrl: '{{ asset('assets/img/dixter/contacto-mapa-pin.svg') }}', iconSize: [60, 60], iconAnchor: [30, 58] }),
            title: 'Dixter',
        }).addTo(mapa).bindTooltip('Dixter - Equipos de Limpieza', { permanent: true, direction: 'right', offset: [18, -30], className: 'tooltip-dixter' });
    }

    // Envío del formulario por AJAX (FormController@contacto)
    const form = document.getElementById('form-contacto');
    const resultado = document.getElementById('contacto-resultado');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const boton = form.querySelector('[type=submit]');
        boton.disabled = true;
        resultado.classList.add('hidden');
        fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(async (r) => ({ ok: r.ok, data: await r.json() }))
            .then(({ ok, data }) => {
                resultado.textContent = data.message || (ok ? 'Mensaje enviado.' : 'Revisá los datos.');
                resultado.className = 'md:col-span-2 text-[15px] ' + (ok ? 'text-green-700' : 'text-marca');
                if (ok) form.reset();
            })
            .catch(() => {
                resultado.textContent = 'Ocurrió un error. Intentá nuevamente.';
                resultado.className = 'md:col-span-2 text-[15px] text-marca';
            })
            .finally(() => { boton.disabled = false; });
    });
});
</script>
@endpush
