@extends('layouts.master')

@section('title', 'Info. de compra')

@php $input = 'w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500'; @endphp

@section('content')

<div>
    <h3 class="text-[20px] font-bold text-gray-500">Tienda · Info. de compra</h3>
    <hr class="mx-6">
</div>

<div class="mx-20 pt-6">
    <div class="border rounded p-4 bg-white">
        <div>
            <h3 class="text-[18px] font-semibold text-gray-600">Textos del carrito y la compra</h3>
            <p class="text-sm text-gray-500 mt-1">Cada salto de línea se respeta en la web.</p>
        </div>
        <hr class="my-3">

        <form method="POST" action="{{ route('adm.info-update') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2" for="retiro_texto">Retiro en local (carrito y compra)</label>
                <textarea id="retiro_texto" name="retiro_texto" rows="3" class="{{ $input }}"
                          placeholder="Venezuela 3557 CABA. Buenos Aires, Argentina.">{{ old('retiro_texto', $info->retiro_texto) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2" for="pago_local_texto">Pago en local (compra)</label>
                <textarea id="pago_local_texto" name="pago_local_texto" rows="4" class="{{ $input }}"
                          placeholder="Consultar por cuotas y promociones vigentes.">{{ old('pago_local_texto', $info->pago_local_texto) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2" for="cuotas_texto">Debajo del precio (detalle del producto)</label>
                <textarea id="cuotas_texto" name="cuotas_texto" rows="2" class="{{ $input }}">{{ old('cuotas_texto', $info->cuotas_texto) }}</textarea>
            </div>

            <h3 class="text-[18px] font-semibold text-gray-600 mt-8">Mails de pedidos</h3>
            <p class="text-sm text-gray-500 mt-1">Reciben el aviso de cada pedido nuevo, fallido o cancelado. Si la casilla de ventas queda vacía, se usa el mail de Contacto.</p>
            <hr class="my-3">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div>
                    <label class="block text-gray-700 font-medium mb-2" for="mail_ventas">Casilla de ventas</label>
                    <input type="email" id="mail_ventas" name="mail_ventas" class="{{ $input }}" placeholder="ventas@dixter.com.ar"
                           value="{{ old('mail_ventas', $info->mail_ventas) }}">
                    @error('mail_ventas')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2" for="mail_ventas_cc">Con copia a (CC, opcional)</label>
                    <input type="email" id="mail_ventas_cc" name="mail_ventas_cc" class="{{ $input }}"
                           value="{{ old('mail_ventas_cc', $info->mail_ventas_cc) }}">
                    @error('mail_ventas_cc')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2" for="privacidad">Política de privacidad</label>
                <p class="text-sm text-gray-500 mb-2">Texto completo de la página <a href="{{ route('privacidad') }}" target="_blank" class="text-blue-600 hover:underline">/politica-de-privacidad</a>, que se abre desde el link de la compra. Admite textos largos, títulos, listas y tablas.</p>
                <textarea id="privacidad" name="privacidad" rows="16" class="ckeditor {{ $input }}">{{ old('privacidad', $info->privacidad) }}</textarea>
            </div>

            <button type="submit" class="border rounded px-6 py-3 bg-white hover:bg-gray-50">Actualizar</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
@include('includes.ckeditor-init')
@endpush
