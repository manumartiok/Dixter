@extends('layouts.master')

@section('title', 'Editar cliente')

@section('content')

   {{-- cabezal --}}
    <div>
        <h3 class="text-[20px] font-bold text-gray-500">Editar cliente</h3>
        <hr class="mx-6">
    </div>

    {{-- Editor --}}
    <div class="mx-20 pt-6">
        <div class="border rounded p-4 bg-white">
            <div>
                <h3 class="text-[18px] font-semibold text-gray-600">{{ $cliente->nombre }}</h3>
            </div>
            <hr class="my-3">

            <form method="POST" action="{{ route('adm.clientes-update', $cliente->id) }}">
                @csrf
                @include('content.admin.partials.cliente-campos')

                <button type="submit" class="border rounded p-4 bg-white hover:bg-gray-50 transition-colors">Actualizar</button>
            </form>
        </div>
    </div>
@endsection
