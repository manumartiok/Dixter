@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

{{-- Cabezal --}}
<div>
    <h3 class="text-[20px] font-bold text-gray-500">Tienda · Compradores</h3>
    <hr class="mx-6">
</div>

{{-- Editor --}}
<div class="mx-20 pt-6" id="app">
    <div class="border border-rounded p-4">

        <h3 class="text-[18px] font-semibold text-gray-600">Editar comprador</h3>
        <hr class="my-3">

        {{-- Errores --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <strong>Errores:</strong>
                <ul class="mt-2">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Mensajes --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('adm.web-usuarios-update') }}">
            @csrf

            <input type="hidden" name="usuarios_id" value="{{ $usuario->id }}">

            {{-- Los mismos datos que pide el formulario de registro --}}
            <div class="grid grid-cols-1 gap-x-6 md:grid-cols-2">
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2" for="usuario">Nombre y apellido *</label>
                    <input type="text" id="usuario" name="usuario" value="{{ old('usuario', $usuario->usuario) }}" required
                           class="w-full border rounded px-4 py-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2" for="email">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" required
                           class="w-full border rounded px-4 py-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2" for="telefono">Celular</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                           class="w-full border rounded px-4 py-2">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2" for="empresa">Empresa / Razón social</label>
                    <input type="text" id="empresa" name="empresa" value="{{ old('empresa', $usuario->empresa) }}"
                           class="w-full border rounded px-4 py-2">
                </div>
            </div>

            {{-- Contraseña --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Nueva contraseña</label>
                <input
                    type="password"
                    name="contraseña"
                    class="w-full border rounded px-4 py-2"
                >
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Repetir nueva contraseña</label>
                <input
                    type="password"
                    name="contraseña_confirmation"
                    class="w-full border rounded px-4 py-2"
                >
            </div>

            <button type="submit" class="border rounded px-6 py-3 bg-white font-semibold">
                Actualizar
            </button>
        </form>
    </div>
</div>

@endsection