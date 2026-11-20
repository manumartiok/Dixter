<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Orden</label>
    <input type="text" name="orden" value="{{ old('orden', $item->orden) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Icono --}}
<div class="mb-4 campo-archivo">
    <label class="block text-gray-700 font-medium mb-2">Ícono (SVG o PNG, 30x30) (2MB máximo)</label>
    <img src="{{ $item->icono }}" alt=""
         class="vista-previa mb-3 max-w-[300px] max-h-[200px] object-cover rounded {{ empty($item->icono) ? 'hidden' : '' }}">
    <input type="file" name="icono" accept="image/*" onchange="previsualizarImagen(this)"
           class="w-full border border-gray-300 rounded px-4 py-2">
    @if (!empty($item->icono))
        <label class="inline-flex items-center gap-2 mt-2 text-sm text-gray-600">
            <input type="checkbox" name="quitar_icono" value="1"> Quitar ícono
        </label>
    @endif
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
