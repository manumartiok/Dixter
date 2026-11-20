@extends('layouts.web')

@section('title', 'Soporte técnico · Dixter')

@section('content')
<div class="mx-auto max-w-[1366px] px-4 md:px-[71px]">
    @include('components.encabezado-pagina', [
        'migas' => [['Soporte técnico', null]],
        'etiqueta' => $soporte?->etiqueta ?: 'Soporte técnico',
        'titulo' => $soporte?->titulo ?: 'Tu equipo, siempre en marcha.',
    ])

    @if ($soporte?->texto)
        <p class="mt-[36px] max-w-[704px] text-[16px] leading-[1.5]">{{ $soporte->texto }}</p>
    @endif

    <div class="mt-[44px] grid grid-cols-1 gap-[24px] sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($soporte?->items ?? [] as $item)
            <div class="flex min-h-[288px] flex-col rounded-[4px] border border-linea bg-white px-[21px] pb-[30px] pt-[55px]">
                @if (!empty($item['icono']))
                    <img src="{{ $item['icono'] }}" alt="" width="30" height="30" class="size-[30px]">
                @endif
                <h2 class="mt-[24px] min-h-[48px] text-[20px] font-semibold leading-[1.2]">{{ $item['titulo'] ?? '' }}</h2>
                <p class="mt-[18px] text-[14px] leading-[1.5]">{{ $item['texto'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
</div>

{{-- Banda roja --}}
<section class="mt-[43px] bg-marca text-white">
    <div class="mx-auto flex max-w-[1366px] flex-col gap-[24px] px-4 py-[29px] md:flex-row md:items-center md:justify-between md:px-[71px] md:pr-[132px]">
        <div>
            <p class="text-[14px] font-bold uppercase leading-[1.5]">{{ $soporte?->cta_etiqueta ?: 'Contactanos' }}</p>
            <h2 class="max-w-[520px] text-[30px] font-semibold leading-[1.2] md:text-[40px]">{{ $soporte?->cta_titulo ?: '¿Necesitas Soporte técnico para tu equipo?' }}</h2>
        </div>
        <a href="{{ route('contacto') }}" class="flex w-full items-center justify-center rounded-[4px] border border-marca bg-white p-[10px] text-[16px] leading-[1.5] text-marca transition hover:bg-[#f5f5f5] md:w-[280px]">
            {{ $soporte?->cta_boton ?: 'Consultar' }}
        </a>
    </div>
</section>
@endsection
