@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div id="app">
    {{-- cabezal  --}}
    <div>
        <h3 class="text-[20px] font-bold text-gray-500">Nosotros</h3>
        <hr class="mx-6">
    </div>
    
    {{-- Editor --}}
    <div class="mx-20 pt-6" >
        <div class="border rounded p-4">
            <div>
                <h3 class="text-[18px] font-semibold  text-gray-600">Editar Nosotros</h3>
            </div>
            <hr class="my-3">
            
            <form method="POST" action="{{ route('adm.nosotros-update') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="nosotro_id" value="{{ $nosotro->id ?? '' }}">

                <div>
                    {{-- Foto principal --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2" for="foto">
                            Foto (recomendado 600x480) / (2MB tamaño máximo)
                        </label>
                        
                        @if (!empty($nosotro->foto))
                            <img src="{{ $nosotro->foto }}" alt="Foto" class="mb-3 w-full max-w-[300px] max-h-[240px] object-cover">
                        @endif
                        
                        <input type="file" name="foto" id="foto"
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>

                    {{-- Título 1 (directo) --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Título 1
                        </label>
                        <input type="text" name="texto1"
                            value="{{ $nosotro->texto1 ?? '' }}"
                            class="w-full border border-gray-300 rounded px-4 py-2">
                    </div>

                    {{-- Título 2 (directo) --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Título 2 <span class="font-normal text-gray-500">(título de las tarjetas, ej: ¿Por qué elegirnos?)</span>
                        </label>
                        <input type="text" name="texto2"
                            value="{{ $nosotro->texto2 ?? '' }}"
                            class="w-full border border-gray-300 rounded px-4 py-2">
                    </div>

                    {{-- Descripción (directo) --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Descripción
                        </label>
                        <textarea name="descripcion" class="ckeditor w-full rounded" rows="6">
                            {!! $nosotro->descripcion ?? '' !!}
                        </textarea>
                    </div>


                    {{-- Logos 1 a 4: tarjetas de "¿Por qué elegirnos?" --}}
                    @foreach ([1, 2, 3, 4] as $n)
                        <div class="mb-6 rounded border border-gray-200 p-4">
                            <h4 class="mb-3 font-semibold text-gray-600">Logo {{ $n }}</h4>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2" for="icono{{ $n }}">
                                    Ícono (recomendado 60x60, SVG o PNG) / (2MB tamaño máximo)
                                </label>
                                @if (!empty($nosotro->{"icono$n"}))
                                    <img src="{{ $nosotro->{"icono$n"} }}" alt="Logo {{ $n }}" class="mb-3 h-[60px] w-[60px] object-contain">
                                @endif
                                <input type="file" name="icono{{ $n }}" id="icono{{ $n }}" accept="image/*"
                                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 font-medium mb-2">Título logo {{ $n }}</label>
                                <input type="text" name="nombre_icono{{ $n }}" value="{{ $nosotro->{"nombre_icono$n"} ?? '' }}"
                                    class="w-full border border-gray-300 rounded px-4 py-2">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Texto logo {{ $n }}</label>
                                <textarea name="texto_icono{{ $n }}" class="ckeditor w-full rounded" rows="4">{!! $nosotro->{"texto_icono$n"} ?? '' !!}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <button type="submit" class="border rounded p-4 bg-white">Actualizar</button>
            </form>
        </div>            
    </div>
</div>
@endsection

@push('scripts')
@include('includes.ckeditor-init')
@endpush