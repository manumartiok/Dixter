@extends('layouts.web')

@section('title', 'Aplicaciones · Dixter')

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[100px] md:px-[71px]">
    @include('components.encabezado-pagina', [
        'migas' => [['Aplicaciones', null]],
        'etiqueta' => 'Aplicaciones',
        'titulo' => $home?->sectores_titulo ?: 'Equipos pensados para cada entorno de trabajo.',
    ])

    <div class="mt-[50px] grid grid-cols-1 gap-[24px] sm:grid-cols-2 lg:grid-cols-3 lg:gap-x-[32px]">
        @foreach ($sectores as $sector)
            <a href="{{ $sector->link ?: route('productos') }}"
               class="group flex min-h-[317px] flex-col rounded-[4px] border border-linea bg-white px-[20px] py-[32px] transition hover:shadow-[0_0_10px_rgba(0,0,0,0.12)]">
                @if ($sector->icono)
                    <img src="{{ $sector->icono }}" alt="" width="30" height="30" class="size-[30px]">
                @endif
                <h2 class="mt-[22px] max-w-[290px] text-[20px] font-semibold leading-[1.2]">{{ $sector->t('nombre') }}</h2>
                <p class="mt-[22px] max-w-[290px] text-[14px] leading-[1.5]">{{ $sector->t('descripcion') }}</p>
                <span class="mt-auto flex items-center gap-[8px] pt-[24px] text-[16px] font-semibold leading-[1.2]">
                    Ver equipos recomendados
                    <img src="{{ asset('assets/img/dixter/icon-move-right.svg') }}" alt="" width="24" height="24" class="transition-transform group-hover:translate-x-1">
                </span>
            </a>
        @endforeach
    </div>
</div>
@endsection
