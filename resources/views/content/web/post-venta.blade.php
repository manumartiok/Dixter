@extends('layouts.web')

@section('title', 'Post Venta · Dixter')

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[140px] md:px-[71px]">
    @include('components.encabezado-pagina', [
        'migas' => [['Post Venta', null]],
        'etiqueta' => $textos?->t('etiqueta') ?: 'Post Venta',
        'titulo' => $textos?->t('titulo') ?: 'Estamos con vos después de la compra.',
    ])

    <div class="mt-[60px] md:mt-[92px]">
        @include('components.postventa-tarjetas')
    </div>
</div>
@endsection
