{{-- Nombre --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Nombre *</label>
    <input type="text" name="nombre" required value="{{ old('nombre', $item->nombre) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Advertencia --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Advertencia</label>
    <p class="text-sm text-gray-500 mb-2">Se muestra arriba de los videos, después de "Advertencia.". Dejalo vacío para no mostrar el recuadro.</p>
    <textarea name="advertencia" rows="2" placeholder="Leer el manual del equipo, transportar sobre las ruedas traseras y usar el producto limpiador recomendado."
              class="w-full border border-gray-300 rounded px-4 py-2">{{ old('advertencia', $item->advertencia) }}</textarea>
</div>

{{-- Videos --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Videos</label>
    <p class="text-sm text-gray-500 mb-2">MP4, WebM o MOV, hasta 200 MB cada uno. Se reproducen en la misma página.</p>
    <div id="lista-videos" class="space-y-3" data-siguiente="{{ count($item->videos ?? []) }}">
        @foreach (array_values($item->videos ?? []) as $i => $fila)
            <div class="fila-lista border rounded p-3 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Nombre</label>
                        <input type="text" name="videos[{{ $i }}][titulo]" value="{{ $fila['titulo'] ?? '' }}" class="w-full border border-gray-300 rounded px-4 py-2">
                    </div>
                    <div class="campo-archivo">
                        <label class="block text-sm text-gray-600 mb-1">Video</label>
                        <input type="hidden" name="videos[{{ $i }}][archivo_actual]" value="{{ $fila['archivo'] ?? '' }}">
                        @if (!empty($fila['archivo']))
                            <video src="{{ $fila['archivo'] }}" controls preload="metadata" class="mb-2 w-full max-w-[320px] rounded bg-black"></video>
                        @endif
                        <input type="file" name="videos[{{ $i }}][archivo]" accept="video/mp4,video/webm,video/quicktime" class="w-full border border-gray-300 rounded px-4 py-2">
                    </div>
                </div>
                <button type="button" onclick="quitarFila(this)" class="mt-2 text-sm text-red-600 hover:underline">Quitar</button>
            </div>
        @endforeach
    </div>

    <template id="lista-videos-template">
        <div class="fila-lista border rounded p-3 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nombre</label>
                    <input type="text" name="videos[__i__][titulo]" value="" class="w-full border border-gray-300 rounded px-4 py-2">
                </div>
                <div class="campo-archivo">
                    <label class="block text-sm text-gray-600 mb-1">Video</label>
                    <input type="hidden" name="videos[__i__][archivo_actual]" value="">
                    <input type="file" name="videos[__i__][archivo]" accept="video/mp4,video/webm,video/quicktime" class="w-full border border-gray-300 rounded px-4 py-2">
                </div>
            </div>
            <button type="button" onclick="quitarFila(this)" class="mt-2 text-sm text-red-600 hover:underline">Quitar</button>
        </div>
    </template>

    <button type="button" onclick="agregarFila('lista-videos')"
            class="mt-3 border rounded px-4 py-2 bg-white hover:bg-gray-50">
        <i class="fa-solid fa-plus mr-1"></i> Agregar video
    </button>
</div>

{{-- Documentos --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Manuales y documentos descargables</label>
    <p class="text-sm text-gray-500 mb-2">PDF u otro archivo, hasta 20 MB cada uno.</p>
    <div id="lista-documentos" class="space-y-3" data-siguiente="{{ count($item->documentos ?? []) }}">
        @foreach (array_values($item->documentos ?? []) as $i => $fila)
            <div class="fila-lista border rounded p-3 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Nombre</label>
                        <input type="text" name="documentos[{{ $i }}][titulo]" value="{{ $fila['titulo'] ?? '' }}" class="w-full border border-gray-300 rounded px-4 py-2">
                    </div>
                    <div class="campo-archivo">
                        <label class="block text-sm text-gray-600 mb-1">Archivo</label>
                        <input type="hidden" name="documentos[{{ $i }}][archivo_actual]" value="{{ $fila['archivo'] ?? '' }}">
                        @if (!empty($fila['archivo']))
                            <a href="{{ $fila['archivo'] ?? '' }}" target="_blank" class="block text-sm text-blue-600 hover:underline mb-1">Ver actual</a>
                        @endif
                        <input type="file" name="documentos[{{ $i }}][archivo]" class="w-full border border-gray-300 rounded px-4 py-2">
                    </div>
                </div>
                <button type="button" onclick="quitarFila(this)" class="mt-2 text-sm text-red-600 hover:underline">Quitar</button>
            </div>
        @endforeach
    </div>

    <template id="lista-documentos-template">
        <div class="fila-lista border rounded p-3 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nombre</label>
                    <input type="text" name="documentos[__i__][titulo]" value="" class="w-full border border-gray-300 rounded px-4 py-2">
                </div>
                <div class="campo-archivo">
                    <label class="block text-sm text-gray-600 mb-1">Archivo</label>
                    <input type="hidden" name="documentos[__i__][archivo_actual]" value="">
                    <input type="file" name="documentos[__i__][archivo]" class="w-full border border-gray-300 rounded px-4 py-2">
                </div>
            </div>
            <button type="button" onclick="quitarFila(this)" class="mt-2 text-sm text-red-600 hover:underline">Quitar</button>
        </div>
    </template>

    <button type="button" onclick="agregarFila('lista-documentos')"
            class="mt-3 border rounded px-4 py-2 bg-white hover:bg-gray-50">
        <i class="fa-solid fa-plus mr-1"></i> Agregar documento
    </button>
</div>
