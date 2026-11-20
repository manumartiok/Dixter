@extends('layouts.web')

@section('title', 'Compra · Dixter')

@use('App\Models\Producto')
@use('App\Models\Pedido')

@php
    $input = 'mt-[6px] h-[40px] w-full rounded-[4px] border border-linea bg-white px-[15px] text-[14px] placeholder:text-black/50 focus:border-marca focus:outline-none';
    $campos = [
        ['nombre', 'Nombre y apellido*', 'Juan Perez', 'text', 'name'],
        ['dni', 'DNI*', '01.2345.6789', 'text', 'off'],
        ['cuit', 'CUIT/CUIL*', '27123456781', 'text', 'off'],
        ['razon_social', 'Razón social', '-', 'text', 'organization'],
        ['email', 'E-mail*', 'juanperez@mail.com', 'email', 'email'],
        ['celular', 'Celular*', 'Código de área + 1234 5678', 'tel', 'tel'],
        ['direccion', 'Dirección*', 'Dirección', 'text', 'street-address'],
        ['localidad', 'Localidad*', 'Localidad', 'text', 'address-level2'],
        ['provincia', 'Provincia*', 'Provincia', 'text', 'address-level1'],
        ['codigo_postal', 'Código postal*', 'Código postal', 'text', 'postal-code'],
    ];
    $pagoElegido = old('forma_pago', 'pago_local');
@endphp

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[100px] md:px-[71px]">
    <p class="pt-[24px] text-[12px] leading-[1.5]"><a href="{{ route('home') }}" class="font-bold">Inicio</a> &gt; Compra</p>

    @if ($errors->any())
        <div class="mt-[24px] rounded-[4px] border border-marca/30 bg-marca/5 px-[16px] py-[12px] text-[15px] text-marca" role="alert">
            Revisá los datos marcados: {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('compra.realizar') }}" class="mt-[40px] grid grid-cols-1 items-start gap-[24px] md:mt-[76px] lg:grid-cols-[1fr_392px]" novalidate>
        @csrf
        <input type="hidden" name="envio" value="{{ $envio }}">

        {{-- ===== Detalle de facturación ===== --}}
        <section aria-labelledby="titulo-facturacion">
            <h1 id="titulo-facturacion" class="border-b border-linea pb-[18px] pt-[19px] text-[24px] font-semibold leading-[1.2]">Detalle de facturación</h1>

            <div class="mt-[12px] grid grid-cols-1 gap-x-[24px] gap-y-[24px] md:grid-cols-2">
                @foreach ($campos as [$nombre, $label, $placeholder, $tipo, $auto])
                    <div>
                        <label for="campo-{{ $nombre }}" class="text-[16px] leading-[1.5]">{{ $label }}</label>
                        <input id="campo-{{ $nombre }}" name="{{ $nombre }}" type="{{ $tipo }}" autocomplete="{{ $auto }}"
                               value="{{ old($nombre, $previo[$nombre] ?? '') }}" placeholder="{{ $placeholder }}"
                               @if (str_ends_with($label, '*')) required @endif
                               class="{{ $input }} {{ $errors->has($nombre) ? '!border-marca' : '' }}">
                        @error($nombre)<p class="mt-[4px] text-[13px] text-marca">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>

            {{-- Enviar a otra dirección --}}
            <label class="mt-[28px] flex cursor-pointer items-center gap-[13px] text-[14px] leading-[1.5] text-[#17181b]">
                <input type="checkbox" name="otra_direccion" value="1" id="otra-direccion" @checked(old('otra_direccion')) class="caja-check sr-only">
                <span class="check flex size-[18px] shrink-0 items-center justify-center rounded-[2px] border border-linea"></span>
                ¿Enviar a una dirección diferente?
            </label>
            <div id="campos-otra-direccion" class="mt-[16px] grid grid-cols-1 gap-[24px] md:grid-cols-2 {{ old('otra_direccion') ? '' : 'hidden' }}">
                @foreach (['direccion' => 'Dirección*', 'localidad' => 'Localidad*', 'provincia' => 'Provincia*', 'codigo_postal' => 'Código postal*'] as $nombre => $label)
                    <div>
                        <label for="otra-{{ $nombre }}" class="text-[16px] leading-[1.5]">{{ $label }}</label>
                        <input id="otra-{{ $nombre }}" name="otra[{{ $nombre }}]" type="text" value="{{ old('otra.' . $nombre) }}" class="{{ $input }} {{ $errors->has('otra.' . $nombre) ? '!border-marca' : '' }}">
                        @error('otra.' . $nombre)<p class="mt-[4px] text-[13px] text-marca">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>

            <label for="campo-mensaje" class="mt-[28px] block text-[16px] leading-[1.5]">Observaciones / Aclaraciones</label>
            <textarea id="campo-mensaje" name="mensaje" rows="8" placeholder="Días de entrega, direccion, horarios,etc"
                      class="mt-[6px] w-full rounded-[4px] border border-linea px-[15px] py-[14px] text-[14px] placeholder:text-black/50 focus:border-marca focus:outline-none">{{ old('mensaje') }}</textarea>
        </section>

        {{-- ===== Pedido ===== --}}
        <aside class="rounded-[4px] border border-linea" aria-labelledby="titulo-pedido">
            <h2 id="titulo-pedido" class="border-b border-linea px-[22px] pb-[18px] pt-[18px] text-[24px] font-semibold leading-[1.2]">Pedido</h2>

            <div class="px-[22px]">
                {{-- Productos --}}
                <div class="border-b border-linea py-[22px]">
                    <p class="text-[20px] font-bold leading-[1.5]">{{ $lineas->count() > 1 ? 'Productos' : 'Producto' }}</p>
                    @foreach ($lineas as $linea)
                        <div class="mt-[8px] {{ $loop->first ? '' : 'border-t border-linea pt-[12px]' }}">
                            <p class="text-[20px] font-semibold leading-[1.2]">{{ $linea->producto->nombre }}@if ($linea->cantidad > 1) <span class="font-normal">× {{ $linea->cantidad }}</span>@endif</p>
                            <div class="mt-[8px] text-right">
                                <p class="text-[26px] font-extrabold leading-[1.2]">{{ Producto::formatearPrecio($linea->total) }}</p>
                                @if ($linea->producto->tieneIva())
                                    <p class="text-[14px] leading-[1.5] text-[#17181b]/60">{{ Producto::formatearPrecio($linea->total_sin_iva) }} sin IVA</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Envío --}}
                <div class="border-b border-linea py-[22px]">
                    <p class="text-[20px] font-bold leading-[1.5] text-[#17181b]">Envio</p>
                    <p class="mt-[8px] flex justify-between text-[16px] leading-[1.5]">{{ Pedido::ENVIOS[$envio][0] }} <b class="text-[14px]">{{ Pedido::ENVIOS[$envio][1] }}</b></p>
                    @if ($envio === 'retiro_local' && $info?->retiro_texto)
                        <p class="mt-[6px] whitespace-pre-line text-[14px] leading-[1.5] text-[#969696]">{{ $info->retiro_texto }}</p>
                    @endif
                    <a href="{{ route('web-carrito') }}" class="mt-[6px] inline-block text-[13px] text-marca underline">Cambiar envío</a>
                </div>

                {{-- Pago --}}
                <fieldset class="border-b border-linea py-[22px]">
                    <legend class="sr-only">Forma de pago</legend>
                    <p class="text-[20px] font-bold leading-[1.5] text-[#17181b]">Pago</p>
                    <label class="mt-[12px] flex cursor-pointer items-start gap-[6px]">
                        <input type="radio" name="forma_pago" value="pago_local" @checked($pagoElegido === 'pago_local') class="caja-check sr-only">
                        <span class="check mt-[3px] flex size-[18px] shrink-0 items-center justify-center rounded-[2px] border border-linea"></span>
                        <span>
                            <span class="text-[16px] leading-[1.5]">Pago en local</span>
                            <span class="text-[14px] font-light leading-[1.5]">(Contado / Tarjeta de débito / Tarjeta de crédito / Transferencia bancaria)</span>
                            @if ($info?->pago_local_texto)
                                <span class="mt-[8px] block whitespace-pre-line text-[14px] leading-[1.5] text-[#969696]">{{ $info->pago_local_texto }}</span>
                            @endif
                        </span>
                    </label>
                    @if ($mercadoPago)
                        <label class="mt-[18px] flex cursor-pointer items-start gap-[6px]">
                            <input type="radio" name="forma_pago" value="mercado_pago" @checked($pagoElegido === 'mercado_pago') class="caja-check sr-only">
                            <span class="check mt-[3px] flex size-[18px] shrink-0 items-center justify-center rounded-[2px] border border-linea"></span>
                            <span class="text-[16px] leading-[1.5]">Mercado Pago</span>
                        </label>
                    @endif
                    @error('forma_pago')<p class="mt-[6px] text-[13px] text-marca">{{ $message }}</p>@enderror
                </fieldset>

                {{-- Privacidad y términos --}}
                <div class="border-b border-linea py-[22px]">
                    <p class="text-[14px] leading-[1.5]">
                        Sus datos personales se utilizarán para respaldar su experiencia en este sitio web, para administrar el acceso a su cuenta y para otros fines descritos en nuestras
                        {{-- Se abre en otra pestaña para no perder los datos cargados en la compra --}}
                        <a href="{{ route('privacidad') }}" target="_blank" rel="noopener" class="font-bold text-[#2540ff] hover:underline">Política de privacidad</a>.
                    </p>
                    <label class="mt-[20px] flex cursor-pointer items-start gap-[12px]">
                        <input type="checkbox" name="terminos" value="1" @checked(old('terminos')) required class="caja-check sr-only">
                        <span class="check mt-[3px] flex size-[18px] shrink-0 items-center justify-center rounded-[2px] border {{ $errors->has('terminos') ? 'border-marca' : 'border-linea' }}"></span>
                        <span class="text-[16px] leading-[1.5]">Acepto los términos y condiciones de privacidad</span>
                    </label>
                    @error('terminos')<p class="mt-[6px] text-[13px] text-marca">{{ $message }}</p>@enderror
                </div>

                {{-- Total --}}
                <div class="flex items-start justify-between border-b border-linea py-[22px]">
                    <p class="text-[24px] font-bold leading-[1.5]">Total</p>
                    <div class="text-right">
                        <p class="text-[26px] font-extrabold leading-[1.2]">{{ Producto::formatearPrecio($totales['total']) }}</p>
                        @if ($totales['muestra_iva'])
                            <p class="text-[14px] leading-[1.5] text-[#17181b]/60">{{ Producto::formatearPrecio($totales['subtotal']) }} sin IVA</p>
                        @endif
                    </div>
                </div>

                <div class="py-[22px]">
                    <button type="submit" id="btn-realizar-compra" class="w-full rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d] disabled:opacity-60">
                        Realizar compra
                    </button>
                </div>
            </div>
        </aside>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const otra = document.getElementById('otra-direccion');
    otra?.addEventListener('change', () => document.getElementById('campos-otra-direccion').classList.toggle('hidden', !otra.checked));

    // Evita el doble envío del pedido
    document.querySelector('form[action="{{ route('compra.realizar') }}"]').addEventListener('submit', () => {
        const boton = document.getElementById('btn-realizar-compra');
        setTimeout(() => { boton.disabled = true; boton.textContent = 'Procesando...'; }, 0);
    });
});
</script>
@endpush
