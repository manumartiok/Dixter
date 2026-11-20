@extends('layouts.master')

@section('title', 'Categorías')

@php $input = 'border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500'; @endphp

@section('content')

<div>
    <h3 class="text-[20px] font-bold text-gray-500">Productos · Categorías</h3>
    <hr class="mx-6">
</div>

<div class="p-4 space-y-4">
    @if ($errors->any())
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    {{-- Alta --}}
    <form method="POST" action="{{ route('adm.categorias-store') }}" class="flex flex-wrap items-end gap-3 rounded-2xl border bg-white px-6 py-4 shadow">
        @csrf
        <div>
            <label class="block text-sm text-gray-600 mb-1">Nueva categoría</label>
            <input type="text" name="nombre" required placeholder="Aspiradoras" class="{{ $input }} w-[260px]">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Dentro de</label>
            <select name="parent_id" class="{{ $input }} w-[280px]">
                <option value="">— Categoría principal —</option>
                @foreach ($padres as $id => $nombre)
                    <option value="{{ $id }}" @selected(old('parent_id') == $id)>{{ $nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Orden</label>
            <input type="text" name="orden" placeholder="AA" class="{{ $input }} w-[90px]">
        </div>
        <button class="border rounded px-4 py-2 bg-white hover:bg-gray-50"><i class="fa-solid fa-plus mr-1"></i> Agregar</button>
        <p class="w-full text-xs text-gray-500">Hasta 3 niveles: <b>Máquinas de limpieza</b> › <b>Aspiradoras</b> › <b>Profesional Serie 300</b>. Los productos se asignan a la categoría más específica.</p>
    </form>

    {{-- Árbol --}}
    <div class="overflow-x-auto rounded-2xl shadow">
        <table class="min-w-full border border-gray-200 bg-white">
            <thead class="bg-gray-100">
                <tr class="text-gray-700 text-sm uppercase">
                    <th class="px-4 py-3 text-left">Categoría</th>
                    <th class="px-4 py-3 text-center">Productos</th>
                    <th class="px-4 py-3 text-center">Activa</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @forelse ($arbol as $nivel1)
                    @include('content.admin.partials.categoria-fila', ['categoria' => $nivel1, 'nivel' => 1])
                    @foreach ($nivel1->hijas as $nivel2)
                        @include('content.admin.partials.categoria-fila', ['categoria' => $nivel2, 'nivel' => 2])
                        @foreach ($nivel2->hijas as $nivel3)
                            @include('content.admin.partials.categoria-fila', ['categoria' => $nivel3, 'nivel' => 3])
                        @endforeach
                    @endforeach
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Todavía no hay categorías.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function editarCategoria(id) {
        document.getElementById('ver-' + id).classList.toggle('hidden');
        document.getElementById('editar-' + id).classList.toggle('hidden');
    }
</script>
@endsection
