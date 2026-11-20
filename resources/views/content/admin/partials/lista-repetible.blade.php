{{--
    Lista repetible (mismo formato que los campos "lista" de make:modulo y GestionaArchivos::procesarLista).
    Parámetros:
      $campo      nombre del input / columna json         ej: 'ficha'
      $titulo     label de la lista
      $subcampos  [nombre => [label, tipo]]  tipo: string | imagen | archivo
      $filas      valores actuales (array)
      $boton      texto del botón (opcional)
      $ayuda      texto de ayuda (opcional)
--}}
@php
    $idLista = 'lista-' . $campo;
    $filas = array_values($filas ?? []);
    $columnas = count($subcampos) >= 3 ? 'md:grid-cols-3' : 'md:grid-cols-2';
@endphp

<div class="mb-4">
    <label class="block text-gray-700 font-medium mb-1">{{ $titulo }}</label>
    @isset($ayuda)<p class="text-sm text-gray-500 mb-2">{{ $ayuda }}</p>@endisset

    <div id="{{ $idLista }}" class="space-y-3" data-siguiente="{{ count($filas) }}">
        @foreach ($filas as $i => $fila)
            <div class="fila-lista border rounded p-3 bg-gray-50">
                <div class="grid grid-cols-1 {{ $columnas }} gap-3">
                    @foreach ($subcampos as $sub => [$label, $tipo])
                        @include('content.admin.partials.lista-repetible-campo', ['nombre' => "{$campo}[{$i}][{$sub}]", 'valor' => $fila[$sub] ?? '', 'actual' => "{$campo}[{$i}][{$sub}_actual]"])
                    @endforeach
                </div>
                <button type="button" onclick="quitarFila(this)" class="mt-2 text-sm text-red-600 hover:underline">Quitar fila</button>
            </div>
        @endforeach
    </div>

    <template id="{{ $idLista }}-template">
        <div class="fila-lista border rounded p-3 bg-gray-50">
            <div class="grid grid-cols-1 {{ $columnas }} gap-3">
                @foreach ($subcampos as $sub => [$label, $tipo])
                    @include('content.admin.partials.lista-repetible-campo', ['nombre' => "{$campo}[__i__][{$sub}]", 'valor' => '', 'actual' => "{$campo}[__i__][{$sub}_actual]"])
                @endforeach
            </div>
            <button type="button" onclick="quitarFila(this)" class="mt-2 text-sm text-red-600 hover:underline">Quitar fila</button>
        </div>
    </template>

    <button type="button" onclick="agregarFila('{{ $idLista }}')" class="mt-3 border rounded px-4 py-2 bg-white hover:bg-gray-50">
        <i class="fa-solid fa-plus mr-1"></i> {{ $boton ?? 'Agregar fila' }}
    </button>
</div>
