@extends('layouts.web')

@section('title', 'Política de privacidad · Dixter')

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[100px] md:px-[71px]">
    @include('components.encabezado-pagina', [
        'migas' => [['Política de privacidad', null]],
        'etiqueta' => 'Legales',
        'titulo' => 'Política de privacidad',
    ])

    @if (filled($info?->privacidad))
        {{-- Texto escrito en Tienda > Info. de compra (CKEditor) --}}
        <div class="texto-legal mt-[40px] max-w-[900px] text-[16px] leading-[1.6] md:mt-[54px]">{!! $info->privacidad !!}</div>
    @else
        <p class="mt-[40px] text-[16px] leading-[1.5] text-black/60">Pronto vas a encontrar acá nuestra política de privacidad.</p>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* El texto viene del editor del admin: títulos, listas, links y tablas con estilo propio */
    .texto-legal > * + * { margin-top: 1em; }
    .texto-legal h2 { margin-top: 1.6em; font-size: 26px; font-weight: 600; line-height: 1.2; }
    .texto-legal h3 { margin-top: 1.4em; font-size: 21px; font-weight: 600; line-height: 1.2; }
    .texto-legal h4 { margin-top: 1.2em; font-size: 18px; font-weight: 700; }
    .texto-legal ul { list-style: disc; padding-left: 1.4em; }
    .texto-legal ol { list-style: decimal; padding-left: 1.4em; }
    .texto-legal li + li { margin-top: .35em; }
    .texto-legal a { color: #C32922; text-decoration: underline; }
    .texto-legal blockquote { border-left: 3px solid #DEDFE0; padding-left: 1em; color: rgba(0, 0, 0, .7); }
    .texto-legal table { width: 100%; border-collapse: collapse; }
    .texto-legal td, .texto-legal th { border: 1px solid #DEDFE0; padding: 8px 12px; text-align: left; }
    .texto-legal figure.table { overflow-x: auto; }
</style>
@endpush
