@extends('layouts.web')

@section('title', 'Carrito · Dixter')

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[100px] md:px-[71px]">
    <p class="pt-[24px] text-[12px] leading-[1.5]"><a href="{{ route('home') }}" class="font-bold">Inicio</a> &gt; Carrito</p>

    <div class="mt-[40px] grid grid-cols-1 items-start gap-[24px] md:mt-[76px] lg:grid-cols-[1fr_392px]">
        <div>
            <section class="rounded-[4px] border border-linea" aria-labelledby="titulo-carrito">
                <h1 id="titulo-carrito" class="border-b border-linea px-[22px] pb-[18px] pt-[18px] text-[24px] font-semibold leading-[1.2]">Carrito</h1>
                <div id="carrito-lineas">
                    @include('components.carrito-lineas')
                </div>
            </section>
            <a href="{{ route('productos') }}" class="mt-[28px] inline-flex items-center gap-[8px] text-[16px] font-bold leading-[1.5] text-[#17181b] hover:text-marca">
                <span aria-hidden="true">‹</span> Seguir comprando
            </a>
        </div>

        <aside id="carrito-resumen" class="rounded-[4px] border border-linea" aria-label="Total del carrito">
            @include('components.carrito-resumen')
        </aside>
    </div>
</div>
@endsection
