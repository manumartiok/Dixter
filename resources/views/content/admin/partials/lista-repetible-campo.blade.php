{{-- Un subcampo de lista-repetible ($label, $tipo, $nombre, $valor, $actual) --}}
@if ($tipo === 'imagen')
    <div class="campo-archivo">
        <label class="block text-sm text-gray-600 mb-1">{{ $label }}</label>
        <input type="hidden" name="{{ $actual }}" value="{{ $valor }}">
        <img src="{{ $valor }}" alt="" class="vista-previa h-[80px] object-cover rounded mb-2 {{ $valor ? '' : 'hidden' }}">
        <input type="file" name="{{ $nombre }}" accept="image/*" onchange="previsualizarImagen(this)" class="w-full border border-gray-300 rounded px-4 py-2">
    </div>
@elseif ($tipo === 'video')
    <div class="campo-archivo">
        <label class="block text-sm text-gray-600 mb-1">{{ $label }}</label>
        <input type="hidden" name="{{ $actual }}" value="{{ $valor }}">
        @if ($valor)
            <video src="{{ $valor }}" controls preload="metadata" class="mb-2 w-full max-w-[320px] rounded bg-black"></video>
        @endif
        <input type="file" name="{{ $nombre }}" accept="video/mp4,video/webm,video/quicktime" class="w-full border border-gray-300 rounded px-4 py-2">
    </div>
@elseif ($tipo === 'archivo')
    <div class="campo-archivo">
        <label class="block text-sm text-gray-600 mb-1">{{ $label }}</label>
        <input type="hidden" name="{{ $actual }}" value="{{ $valor }}">
        @if ($valor)
            <a href="{{ $valor }}" target="_blank" class="block text-sm text-blue-600 hover:underline mb-1">Ver actual</a>
        @endif
        <input type="file" name="{{ $nombre }}" class="w-full border border-gray-300 rounded px-4 py-2">
    </div>
@else
    <div>
        <label class="block text-sm text-gray-600 mb-1">{{ $label }}</label>
        <input type="text" name="{{ $nombre }}" value="{{ $valor }}" class="w-full border border-gray-300 rounded px-4 py-2">
    </div>
@endif
