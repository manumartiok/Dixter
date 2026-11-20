@extends('layouts.master')

@section('title', 'Crear cliente')

@section('content')

   {{-- cabezal --}}
    <div>
        <h3 class="text-[20px] font-bold text-gray-500">Crear cliente</h3>
        <hr class="mx-6">
    </div>

    {{-- Editor --}}
    <div class="mx-20 pt-6">
        <div class="border rounded p-4 bg-white">
            <div>
                <h3 class="text-[18px] font-semibold text-gray-600">Nuevo cliente del Área clientes</h3>
                <p class="text-sm text-gray-500 mt-1">El cliente inicia sesión desde el botón "Área clientes" del sitio con el mail y la contraseña que cargues acá. Va a ver todos los productos cargados en Área clientes &gt; Productos.</p>
            </div>
            <hr class="my-3">

            <form method="POST" action="{{ route('adm.clientes-store') }}">
                @csrf
                @include('content.admin.partials.cliente-campos')

                <button type="submit" class="border rounded p-4 bg-white hover:bg-gray-50 transition-colors">Crear</button>
            </form>
        </div>
    </div>
@endsection
