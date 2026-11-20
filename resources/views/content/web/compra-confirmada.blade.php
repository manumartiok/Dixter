@extends('layouts.web')

@section('title', 'Pedido ' . $pedido->numero() . ' · Dixter')

@php
    $esMp = $pedido->forma_pago === 'mercado_pago';
    $estado = match (true) {
        !$esMp, $pedido->pago_estado === 'aprobado' => 'confirmado',
        $pedido->resultado_pago === 'rechazado' => 'rechazado',
        $pedido->resultado_pago === 'pendiente' => 'en_curso',
        default => 'sin_pagar',
    };
@endphp

@section('content')
<div class="mx-auto flex max-w-[1366px] justify-center px-4 py-[80px] md:px-[71px] md:py-[120px]">
    {{-- Figma: Pedido confirmado (642x343) --}}
    <div class="w-full max-w-[642px] rounded-[4px] border border-[#dcdcdc] bg-white px-[24px] pb-[60px] pt-[70px] text-center md:px-[49px] md:pt-[84px]">
        @if (session('error_mp'))
            <p class="mb-[20px] rounded-[4px] bg-marca/5 px-[12px] py-[8px] text-[14px] text-marca">{{ session('error_mp') }}</p>
        @endif

        @switch($estado)
            @case('confirmado')
                <h1 class="text-[32px] font-semibold leading-[1.2] text-marca">Pedido confirmado</h1>
                <p class="mt-[30px] text-[15px] leading-[1.5]">
                    Su pedido <b class="font-semibold">{{ $pedido->numero() }}</b> está en proceso y te avisaremos por email cuando esté listo.
                    Si tienes alguna pregunta, no dudes en contactarnos.
                </p>
                @break

            @case('en_curso')
                <h1 class="text-[32px] font-semibold leading-[1.2] text-marca">Pago en proceso</h1>
                <p class="mt-[30px] text-[15px] leading-[1.5]">
                    Mercado Pago está procesando el pago del pedido <b class="font-semibold">{{ $pedido->numero() }}</b>.
                    Te avisaremos por email cuando se acredite.
                </p>
                @break

            @case('rechazado')
                <h1 class="text-[32px] font-semibold leading-[1.2] text-marca">El pago no se completó</h1>
                <p class="mt-[30px] text-[15px] leading-[1.5]">
                    Mercado Pago rechazó el pago del pedido <b class="font-semibold">{{ $pedido->numero() }}</b>. Podés intentarlo de nuevo con otro medio.
                </p>
                @break

            @default
                <h1 class="text-[32px] font-semibold leading-[1.2] text-marca">Falta el pago</h1>
                <p class="mt-[30px] text-[15px] leading-[1.5]">
                    Tu pedido <b class="font-semibold">{{ $pedido->numero() }}</b> quedó registrado. Para confirmarlo, completá el pago con Mercado Pago.
                </p>
        @endswitch

        <div class="mt-[40px] flex flex-col items-center justify-center gap-[12px] sm:flex-row">
            @if ($pedido->puede_pagar_mercado_pago)
                <a href="{{ route('compra.mp.pagar', $pedido) }}" class="flex w-[247px] items-center justify-center rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d]">
                    Pagar con Mercado Pago
                </a>
                <a href="{{ route('productos') }}" class="flex w-[247px] items-center justify-center rounded-[4px] border border-marca bg-white p-[10px] text-[16px] leading-[1.5] text-marca transition hover:bg-marca hover:text-white">
                    Volver a productos
                </a>
            @else
                <a href="{{ route('productos') }}" class="flex w-[247px] items-center justify-center rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d]">
                    Volver a productos
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
