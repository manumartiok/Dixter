@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

   {{-- cabezal --}}
    <div>
        <h3 class="text-[20px] font-bold text-gray-500">Soporte técnico</h3>
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

            <form method="POST" action="{{ route('adm.soporte-update') }}" enctype="multipart/form-data">
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

                {{-- Texto --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Texto</label>
                    <input type="text" name="texto" value="{{ old('texto', $item->texto) }}"
                           class="w-full border border-gray-300 rounded px-4 py-2">
                </div>

                {{-- Items --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Tarjetas</label>
                    <div id="lista-items" class="space-y-3" data-siguiente="{{ count($item->items ?? []) }}">
                        @foreach (array_values($item->items ?? []) as $i => $fila)
                            <div class="fila-lista border rounded p-3 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="campo-archivo">
                                        <label class="block text-sm text-gray-600 mb-1">Ícono (SVG o PNG, 30x30)</label>
                                        <input type="hidden" name="items[{{ $i }}][icono_actual]" value="{{ $fila['icono'] ?? '' }}">
                                        <img src="{{ $fila['icono'] ?? '' }}" alt="" class="vista-previa h-[80px] object-cover rounded mb-2 {{ empty($fila['icono']) ? 'hidden' : '' }}">
                                        <input type="file" name="items[{{ $i }}][icono]" accept="image/*" onchange="previsualizarImagen(this)" class="w-full border border-gray-300 rounded px-4 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Título</label>
                                        <input type="text" name="items[{{ $i }}][titulo]" value="{{ $fila['titulo'] ?? '' }}" class="w-full border border-gray-300 rounded px-4 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Texto</label>
                                        <textarea name="items[{{ $i }}][texto]" rows="3" class="w-full border border-gray-300 rounded px-4 py-2">{{ $fila['texto'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                <button type="button" onclick="quitarFila(this)" class="mt-2 text-sm text-red-600 hover:underline">Quitar fila</button>
                            </div>
                        @endforeach
                    </div>

                    <template id="lista-items-template">
                        <div class="fila-lista border rounded p-3 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="campo-archivo">
                                    <label class="block text-sm text-gray-600 mb-1">Ícono (SVG o PNG, 30x30)</label>
                                    <input type="hidden" name="items[__i__][icono_actual]" value="">
                                    <img src="" alt="" class="vista-previa h-[80px] object-cover rounded mb-2 hidden">
                                    <input type="file" name="items[__i__][icono]" accept="image/*" onchange="previsualizarImagen(this)" class="w-full border border-gray-300 rounded px-4 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Título</label>
                                    <input type="text" name="items[__i__][titulo]" value="" class="w-full border border-gray-300 rounded px-4 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Texto</label>
                                    <textarea name="items[__i__][texto]" rows="3" class="w-full border border-gray-300 rounded px-4 py-2"></textarea>
                                </div>
                            </div>
                            <button type="button" onclick="quitarFila(this)" class="mt-2 text-sm text-red-600 hover:underline">Quitar fila</button>
                        </div>
                    </template>

                    <button type="button" onclick="agregarFila('lista-items')"
                            class="mt-3 border rounded px-4 py-2 bg-white hover:bg-gray-50">
                        <i class="fa-solid fa-plus mr-1"></i> Agregar fila
                    </button>
                </div>

                {{-- Cta Etiqueta --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Banda roja: etiqueta</label>
                    <input type="text" name="cta_etiqueta" value="{{ old('cta_etiqueta', $item->cta_etiqueta) }}"
                           class="w-full border border-gray-300 rounded px-4 py-2">
                </div>

                {{-- Cta Titulo --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Banda roja: título</label>
                    <input type="text" name="cta_titulo" value="{{ old('cta_titulo', $item->cta_titulo) }}"
                           class="w-full border border-gray-300 rounded px-4 py-2">
                </div>

                {{-- Cta Boton --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Banda roja: texto del botón</label>
                    <input type="text" name="cta_boton" value="{{ old('cta_boton', $item->cta_boton) }}"
                           class="w-full border border-gray-300 rounded px-4 py-2">
                </div>

                <button type="submit" class="border rounded p-4 bg-white hover:bg-gray-50 transition-colors">Actualizar</button>
            </form>
        </div>
    </div>

    @include('includes.modulo-scripts')
@endsection
