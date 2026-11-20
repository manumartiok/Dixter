@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

   {{-- cabezal --}}
    <div>
        <h3 class="text-[20px] font-bold text-gray-500">Área clientes · Productos</h3>
        <hr class="mx-6">
    </div>

   {{-- Listado --}}
   <div class="p-4">
        <div class="overflow-x-auto rounded-2xl shadow">
            <div class="flex justify-end px-6 py-3 border rounded">
                <a href="{{ route('adm.producto-cliente-creador') }}"
                   class="hover:text-gray-400 hover:scale-105 transition-transform duration-300">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </div>

            <table class="min-w-full border border-gray-200 bg-white">
                <thead class="bg-gray-100">
                    <tr class="text-gray-700 text-sm uppercase">
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-center">Videos</th>
                        <th class="px-4 py-3 text-center">Documentos</th>
                        <th class="px-4 py-3 text-center">Activo</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody id="producto-cliente-tabla" class="text-gray-600">
                    @foreach($items as $item)
                        <tr class="border-t hover:bg-gray-50" data-id="{{ $item->id }}">
                            <td class="px-4 py-3 text-left">
                                {{ $item->nombre }}
                            </td>
                            <td class="px-4 py-3 text-center">{{ count($item->videosCargados()) }}</td>
                            <td class="px-4 py-3 text-center">{{ count($item->documentosCargados()) }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('adm.producto-cliente-switch', $item->id) }}"
                                   class="badge {{ $item->active ? 'bg-green-400' : 'bg-red-400' }} text-white px-4 py-2 rounded">
                                    {{ $item->active ? 'Activo' : 'Inactivo' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('adm.producto-cliente-editor', $item->id) }}" class="text-blue-600 hover:underline font-bold">Editar</a>
                                <span>|</span>
                                <a href="{{ route('adm.producto-cliente-destroy', $item->id) }}" class="text-red-600 hover:underline font-bold"
                                   onclick="return confirm('¿Eliminar este producto? Los clientes dejan de verlo.')">Borrar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
