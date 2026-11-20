<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Orden</label>
    <input type="text" name="orden" value="{{ old('orden', $item->orden) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Nombre --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $item->nombre) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Direccion --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Dirección</label>
    <input type="text" name="direccion" value="{{ old('direccion', $item->direccion) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Localidad --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Localidad</label>
    <input type="text" name="localidad" value="{{ old('localidad', $item->localidad) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Provincia --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Provincia</label>
    <input type="text" name="provincia" value="{{ old('provincia', $item->provincia) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Web --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Web</label>
    <input type="text" name="web" value="{{ old('web', $item->web) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Email --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Email</label>
    <input type="text" name="email" value="{{ old('email', $item->email) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Telefono --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Teléfono</label>
    <input type="text" name="telefono" value="{{ old('telefono', $item->telefono) }}"
           class="w-full border border-gray-300 rounded px-4 py-2">
</div>

{{-- Ubicación en el mapa --}}
<div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-gray-700 font-medium mb-2">Latitud</label>
        <input type="text" name="latitud" value="{{ old('latitud', $item->latitud) }}" placeholder="-34.6157"
               class="w-full border border-gray-300 rounded px-4 py-2">
    </div>
    <div>
        <label class="block text-gray-700 font-medium mb-2">Longitud</label>
        <input type="text" name="longitud" value="{{ old('longitud', $item->longitud) }}" placeholder="-58.4119"
               class="w-full border border-gray-300 rounded px-4 py-2">
    </div>
    <p class="md:col-span-2 text-sm text-gray-500">En Google Maps: clic derecho sobre el lugar y clic en los números (copia "latitud, longitud"). Pegá cada uno en su campo.</p>
</div>

{{-- Líneas --}}
<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-2">Líneas que vende</label>
    @php $lineasElegidas = old('lineas', $item->lineas ?? []); @endphp
    <div class="flex flex-wrap gap-6">
        @foreach (\App\Models\Distribuidore::LINEAS as $clave => [$texto])
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="lineas[]" value="{{ $clave }}" @checked(in_array($clave, (array) $lineasElegidas))> {{ $texto }}
            </label>
        @endforeach
    </div>
    <p class="text-sm text-gray-500 mt-1">Con más de una línea, en el mapa se muestra con el ícono de "Todas las líneas".</p>
</div>
