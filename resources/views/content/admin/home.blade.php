@extends('layouts.master')

@section('title', 'Home')

@php
    $input = 'w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500';
    $beneficios = $home->beneficios ?? [];
    $iconosBeneficios = ['Técnicos (engranaje)', 'Repuestos (engranajes)', 'Respuesta (camión)'];
@endphp

@section('content')

{{-- Cabezal --}}
<div>
    <h3 class="text-[20px] font-bold text-gray-500">Home</h3>
    <hr class="mx-6">
</div>

{{-- Editor --}}
<div class="mx-20 pt-6">
    <div class="border rounded p-4 bg-white">
        <div>
            <h3 class="text-[18px] font-semibold text-gray-600">Editar Home</h3>
            <p class="text-sm text-gray-500 mt-1">
                El hero se edita en <a class="text-blue-600 hover:underline" href="{{ route('adm.home-ban') }}">Home &gt; Banner</a>,
                las familias en <a class="text-blue-600 hover:underline" href="{{ route('adm.familia') }}">Familias de productos</a>,
                las industrias en <a class="text-blue-600 hover:underline" href="{{ route('adm.sectore') }}">Industrias</a>
                y el bloque Nosotros en <a class="text-blue-600 hover:underline" href="{{ route('adm.nosotros') }}">Nosotros &gt; Contenido</a>.
            </p>
        </div>

        <hr class="my-3">

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('adm.home-update') }}">
            @csrf

            {{-- Soporte técnico / Post venta --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <fieldset class="border rounded p-4">
                    <legend class="px-2 font-semibold text-gray-600">Tarjeta roja (Soporte técnico)</legend>
                    <label class="block text-gray-700 font-medium mb-2">Etiqueta</label>
                    <input type="text" name="soporte_etiqueta" value="{{ old('soporte_etiqueta', $home->soporte_etiqueta) }}" class="{{ $input }} mb-3">
                    <label class="block text-gray-700 font-medium mb-2">Título</label>
                    <input type="text" name="soporte_titulo" value="{{ old('soporte_titulo', $home->soporte_titulo) }}" class="{{ $input }} mb-3">
                    <label class="block text-gray-700 font-medium mb-2">Texto</label>
                    <textarea name="soporte_texto" rows="3" class="{{ $input }}">{{ old('soporte_texto', $home->soporte_texto) }}</textarea>
                </fieldset>

                <fieldset class="border rounded p-4">
                    <legend class="px-2 font-semibold text-gray-600">Tarjeta negra (Post venta)</legend>
                    <label class="block text-gray-700 font-medium mb-2">Etiqueta</label>
                    <input type="text" name="postventa_etiqueta" value="{{ old('postventa_etiqueta', $home->postventa_etiqueta) }}" class="{{ $input }} mb-3">
                    <label class="block text-gray-700 font-medium mb-2">Título</label>
                    <input type="text" name="postventa_titulo" value="{{ old('postventa_titulo', $home->postventa_titulo) }}" class="{{ $input }} mb-3">
                    <label class="block text-gray-700 font-medium mb-2">Texto</label>
                    <textarea name="postventa_texto" rows="3" class="{{ $input }}">{{ old('postventa_texto', $home->postventa_texto) }}</textarea>
                </fieldset>
            </div>

            {{-- Familias --}}
            <fieldset class="border rounded p-4 mt-6">
                <legend class="px-2 font-semibold text-gray-600">Familias de productos (sección negra)</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Etiqueta</label>
                        <input type="text" name="familias_etiqueta" value="{{ old('familias_etiqueta', $home->familias_etiqueta) }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Título</label>
                        <input type="text" name="familias_titulo" value="{{ old('familias_titulo', $home->familias_titulo) }}" class="{{ $input }}">
                    </div>
                </div>
            </fieldset>

            {{-- Beneficios + lema --}}
            <fieldset class="border rounded p-4 mt-6">
                <legend class="px-2 font-semibold text-gray-600">Bloque "Te acompañamos" (fondo gris)</legend>
                @foreach (range(0, 2) as $i)
                    <div class="grid grid-cols-1 md:grid-cols-[180px_1fr_1fr] gap-4 items-end mb-3">
                        <p class="text-sm text-gray-500 pb-2">Ícono: {{ $iconosBeneficios[$i] }}</p>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Texto en rojo</label>
                            <input type="text" name="beneficios[{{ $i }}][destacado]" value="{{ old("beneficios.$i.destacado", $beneficios[$i]['destacado'] ?? '') }}" class="{{ $input }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Resto del texto</label>
                            <input type="text" name="beneficios[{{ $i }}][texto]" value="{{ old("beneficios.$i.texto", $beneficios[$i]['texto'] ?? '') }}" class="{{ $input }}">
                        </div>
                    </div>
                @endforeach
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Frase (texto fino)</label>
                        <input type="text" name="lema" value="{{ old('lema', $home->lema) }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Frase (texto en negrita)</label>
                        <input type="text" name="lema_destacado" value="{{ old('lema_destacado', $home->lema_destacado) }}" class="{{ $input }}">
                    </div>
                </div>
            </fieldset>

            {{-- Industrias --}}
            <fieldset class="border rounded p-4 mt-6 mb-6">
                <legend class="px-2 font-semibold text-gray-600">Industrias</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Etiqueta</label>
                        <input type="text" name="sectores_etiqueta" value="{{ old('sectores_etiqueta', $home->sectores_etiqueta) }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Título</label>
                        <input type="text" name="sectores_titulo" value="{{ old('sectores_titulo', $home->sectores_titulo) }}" class="{{ $input }}">
                    </div>
                </div>
            </fieldset>

            <button type="submit" class="border rounded px-6 py-3 bg-white hover:bg-gray-50 transition">Actualizar</button>
        </form>
    </div>
</div>

@endsection
