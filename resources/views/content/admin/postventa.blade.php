@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

   {{-- cabezal --}}
    <div>
        <h3 class="text-[20px] font-bold text-gray-500">Post Venta</h3>
        <hr class="mx-6">
    </div>

    {{-- Editor --}}
    <div class="mx-20 pt-6">
        <div class="border border-rounded p-4">
            <div>
                <h3 class="text-[18px] font-semibold text-gray-600">Editar contenido</h3>
            </div>
            <hr class="my-3">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('adm.postventa-update') }}" enctype="multipart/form-data">
                @csrf

                {{-- Etiqueta --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Etiqueta</label>
                    <input type="text" name="etiqueta" value="{{ old('etiqueta', $item->etiqueta) }}"
                           class="w-full border border-gray-300 rounded px-4 py-2">
                </div>

                {{-- Titulo --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Título</label>
                    <input type="text" name="titulo" value="{{ old('titulo', $item->titulo) }}"
                           class="w-full border border-gray-300 rounded px-4 py-2">
                </div>

                <button type="submit" class="border rounded p-4 bg-white hover:bg-gray-50 transition-colors">Actualizar</button>
            </form>
        </div>
    </div>

    @include('includes.modulo-scripts')
@endsection
