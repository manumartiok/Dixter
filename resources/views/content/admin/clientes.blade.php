@extends('layouts.master')

@section('title', 'Clientes')

@section('content')

   {{-- cabezal --}}
    <div>
        <h3 class="text-[20px] font-bold text-gray-500">Área clientes · Clientes</h3>
        <hr class="mx-6">
    </div>

   {{-- Listado --}}
   <div class="p-4">
        <div class="overflow-x-auto rounded-2xl shadow">
            <div class="flex justify-between items-center gap-4 px-6 py-3 border rounded bg-white">
                <form method="GET" action="{{ route('adm.clientes') }}" class="flex gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o mail"
                           class="w-[320px] border border-gray-300 rounded px-3 py-1.5 text-sm">
                    <button class="border rounded px-3 py-1.5 text-sm hover:bg-gray-50"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <a href="{{ route('adm.clientes-creador') }}" title="Crear cliente"
                   class="hover:text-gray-400 hover:scale-105 transition-transform duration-300">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </div>

            <table class="min-w-full border border-gray-200 bg-white">
                <thead class="bg-gray-100">
                    <tr class="text-gray-700 text-sm uppercase">
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Mail</th>
                        <th class="px-4 py-3 text-center">Activo</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody class="text-gray-600">
                    @forelse($clientes as $cliente)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $cliente->nombre }}</td>
                            <td class="px-4 py-3">{{ $cliente->email ?: $cliente->usuario }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('adm.clientes-switch', $cliente->id) }}"
                                   class="badge {{ $cliente->active ? 'bg-green-400' : 'bg-red-400' }} text-white px-4 py-2 rounded">
                                    {{ $cliente->active ? 'Activo' : 'Inactivo' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('adm.clientes-editor', $cliente->id) }}" class="text-blue-600 hover:underline font-bold">Editar</a>
                                <span>|</span>
                                <a href="{{ route('adm.clientes-destroy', $cliente->id) }}" class="text-red-600 hover:underline font-bold"
                                   onclick="return confirm('¿Eliminar este cliente?')">Borrar</a>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t">
                            <td colspan="4" class="px-4 py-6 text-center text-gray-400">Todavía no hay clientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $clientes->links() }}</div>
    </div>

@endsection
