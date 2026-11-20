{{-- Fila del árbol de categorías (vista + edición en línea) --}}
<tr class="border-t hover:bg-gray-50">
    <td class="px-4 py-3">
        <div id="ver-{{ $categoria->id }}" style="padding-left: {{ ($nivel - 1) * 28 }}px" class="flex items-center gap-2">
            @if ($nivel > 1)<span class="text-gray-300">└</span>@endif
            <span class="{{ $nivel === 1 ? 'font-bold text-gray-800' : ($nivel === 2 ? 'font-semibold' : '') }}">{{ $categoria->nombre }}</span>
            @if ($categoria->orden)<span class="text-xs text-gray-400">({{ $categoria->orden }})</span>@endif
        </div>

        <form id="editar-{{ $categoria->id }}" method="POST" action="{{ route('adm.categorias-update', $categoria->id) }}"
              class="hidden flex flex-wrap items-center gap-2" style="padding-left: {{ ($nivel - 1) * 28 }}px">
            @csrf
            <input type="text" name="nombre" value="{{ $categoria->nombre }}" required class="border border-gray-300 rounded px-2 py-1 text-sm w-[220px]">
            <select name="parent_id" class="border border-gray-300 rounded px-2 py-1 text-sm w-[220px]">
                <option value="">— Categoría principal —</option>
                @foreach ($padres as $id => $nombre)
                    @continue($id === $categoria->id)
                    <option value="{{ $id }}" @selected($categoria->parent_id == $id)>{{ $nombre }}</option>
                @endforeach
            </select>
            <input type="text" name="orden" value="{{ $categoria->orden }}" placeholder="Orden" class="border border-gray-300 rounded px-2 py-1 text-sm w-[70px]">
            <button class="text-sm border rounded px-3 py-1 bg-white hover:bg-gray-100">Guardar</button>
            <button type="button" onclick="editarCategoria({{ $categoria->id }})" class="text-sm text-gray-500 hover:underline">Cancelar</button>
        </form>
    </td>
    <td class="px-4 py-3 text-center">{{ $conteos[$categoria->id] ?? 0 }}</td>
    <td class="px-4 py-3 text-center">
        <a href="{{ route('adm.categorias-switch', $categoria->id) }}"
           class="badge {{ $categoria->active ? 'bg-green-400' : 'bg-red-400' }} text-white px-4 py-2 rounded">
            {{ $categoria->active ? 'Activa' : 'Inactiva' }}
        </a>
    </td>
    <td class="px-4 py-3 text-center whitespace-nowrap">
        <button type="button" onclick="editarCategoria({{ $categoria->id }})" class="text-blue-600 hover:underline font-bold">Editar</button>
        <span>|</span>
        <form method="POST" action="{{ route('adm.categorias-destroy', $categoria->id) }}" class="inline"
              onsubmit="return confirm('¿Eliminar «{{ $categoria->nombre }}» y sus subcategorías? Los productos quedan sin categoría.')">
            @csrf
            <button class="text-red-600 hover:underline font-bold">Borrar</button>
        </form>
    </td>
</tr>
