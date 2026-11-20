{{-- Campos del creador y el editor de clientes. Al crear solo se piden nombre, email y contraseña. --}}
@if ($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2" for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" required
               placeholder="Mariano Rodríguez"
               class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2" for="email">Mail (para iniciar sesión) *</label>
        <input type="email" id="email" name="email" value="{{ old('email', $cliente->email) }}" required autocomplete="off"
               placeholder="mariano@empresa.com"
               class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2" for="contraseña">
            Contraseña {{ $cliente->exists ? '(dejar vacío para no cambiarla)' : '*' }}
        </label>
        <input type="password" id="contraseña" name="contraseña" autocomplete="new-password" {{ $cliente->exists ? '' : 'required' }}
               class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2" for="contraseña_confirmation">Repetir contraseña {{ $cliente->exists ? '' : '*' }}</label>
        <input type="password" id="contraseña_confirmation" name="contraseña_confirmation" autocomplete="new-password" {{ $cliente->exists ? '' : 'required' }}
               class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>

@if ($cliente->exists)
    <div class="mb-4">
        <input type="hidden" name="active" value="0">
        <label class="inline-flex items-center gap-2 text-gray-700 font-medium">
            <input type="checkbox" name="active" value="1" @checked(old('active', $cliente->active))> Activo (puede iniciar sesión)
        </label>
    </div>
@endif
