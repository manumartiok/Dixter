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

{{-- Nombre --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $item->nombre) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Descripcion --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Descripción</label>
    <input type="text" name="descripcion" value="{{ old('descripcion', $item->descripcion) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Destacado --}}
<div class="mb-4">
    <label class="inline-flex items-center gap-2 text-gray-700 font-medium">
        <input type="hidden" name="destacado" value="0">
        <input type="checkbox" name="destacado" value="1" @checked(old('destacado', $item->destacado))>
        Destacado (se muestra en el Home)
    </label>
</div>

{{-- Link --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Link de "Ver equipos recomendados" (opcional)</label>
    <input type="text" name="link" value="{{ old('link', $item->link) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>
