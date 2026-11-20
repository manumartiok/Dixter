{{-- Catálogo: categoría, precios, IVA, descripción y material público del detalle --}}
@php $categorias = \App\Models\Categoria::opcionesParaSelect(); @endphp

<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Categoría</label>
    <select name="categoria_id" class="w-full border border-gray-300 rounded px-4 py-2">
        <option value="">-- Sin categoría --</option>
        @foreach ($categorias as $id => $nombre)
            <option value="{{ $id }}" @selected(old('categoria_id', $producto->categoria_id ?? null) == $id)>{{ $nombre }}</option>
        @endforeach
    </select>
    <p class="text-sm text-gray-500 mt-1">Elegí la categoría más específica (ej. Profesional Serie 300). Las categorías se editan en <a class="text-blue-600 hover:underline" href="{{ route('adm.categorias') }}">Productos › Categorías</a>.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2">Precio final (con IVA)</label>
        <input type="number" step="0.01" min="0" name="precio" value="{{ old('precio', $producto->precio ?? '') }}" class="w-full border border-gray-300 rounded px-4 py-2">
        <p class="text-sm text-gray-500 mt-1">Vacío = se muestra "Consultar" y no se puede comprar.</p>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2">Precio anterior (oferta)</label>
        <input type="number" step="0.01" min="0" name="precio_anterior" value="{{ old('precio_anterior', $producto->precio_anterior ?? '') }}" class="w-full border border-gray-300 rounded px-4 py-2">
        <p class="text-sm text-gray-500 mt-1">Si es mayor al precio, aparece tachado y con la etiqueta OFERTA.</p>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2">IVA</label>
        <select name="iva" class="w-full border border-gray-300 rounded px-4 py-2">
            @foreach (\App\Models\Producto::IVAS as $iva)
                <option value="{{ $iva }}" @selected((float) old('iva', $producto->iva ?? 21) === (float) $iva)>{{ str_replace('.', ',', $iva) }}%</option>
            @endforeach
        </select>
        <p class="text-sm text-gray-500 mt-1">Con 10,5% o 21% se muestra también el precio "sin IVA".</p>
    </div>
</div>

<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Descripción (al lado de la foto)</label>
    <textarea name="descripcion" rows="8" class="ckeditor w-full border border-gray-300 rounded px-4 py-2">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
</div>

<div class="border-t pt-4 mt-6">
    <h4 class="text-[16px] font-semibold text-gray-600 mb-1">Pestañas del detalle (públicas)</h4>
    <p class="text-sm text-gray-500 mb-4">Las pestañas vacías no se muestran.</p>

    @include('content.admin.partials.lista-repetible', [
        'campo' => 'ficha',
        'titulo' => 'Modelos: características para la tabla comparativa',
        'ayuda' => 'Se compara con los demás productos de la misma categoría. Usá los mismos nombres en todos (Modelo, Consumo, Voltaje, Succión...).',
        'subcampos' => ['nombre' => ['Característica', 'string'], 'valor' => ['Valor', 'string']],
        'filas' => $producto->ficha ?? [],
        'boton' => 'Agregar característica',
    ])

    @include('content.admin.partials.lista-repetible', [
        'campo' => 'informacion',
        'titulo' => 'Información adicional',
        'subcampos' => ['nombre' => ['Dato', 'string'], 'valor' => ['Valor', 'string']],
        'filas' => $producto->informacion ?? [],
        'boton' => 'Agregar dato',
    ])

    @include('content.admin.partials.lista-repetible', [
        'campo' => 'instructivos',
        'titulo' => 'Instructivos (PDF, 10MB máximo)',
        'subcampos' => ['titulo' => ['Título', 'string'], 'archivo' => ['Archivo', 'archivo']],
        'filas' => $producto->instructivos ?? [],
        'boton' => 'Agregar instructivo',
    ])

    @include('content.admin.partials.lista-repetible', [
        'campo' => 'videos_web',
        'titulo' => 'Videos (miniatura recomendada 640x360)',
        'subcampos' => ['titulo' => ['Nombre', 'string'], 'archivo' => ['Video (MP4, WebM o MOV, hasta 200 MB)', 'video']],
        'filas' => $producto->videos_web ?? [],
        'boton' => 'Agregar video',
    ])
</div>

{{-- agregarFila / quitarFila / previsualizarImagen de las listas repetibles --}}
@include('includes.modulo-scripts')
