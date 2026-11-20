{{-- "Total del carrito" (Figma: Carrito, caja derecha). $totales, $info, $envioElegido --}}
@use('App\Models\Producto')
@use('App\Models\Pedido')
<h2 class="border-b border-linea px-[22px] pb-[18px] pt-[18px] text-[24px] font-semibold leading-[1.2]">Total del carrito</h2>

<div class="px-[22px]">
    <div class="flex items-start justify-between border-b border-linea py-[22px]">
        <p class="text-[20px] font-semibold leading-[1.2] text-[#17181b]">Subtotal</p>
        <div class="text-right">
            <p class="text-[26px] font-extrabold leading-[1.2]">{{ Producto::formatearPrecio($totales['total']) }}</p>
            @if ($totales['muestra_iva'])
                <p class="text-[14px] leading-[1.5] text-[#17181b]/60">{{ Producto::formatearPrecio($totales['subtotal']) }} sin IVA</p>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('compra') }}" id="form-ir-a-compra">
        <fieldset class="border-b border-linea py-[22px]">
            <legend class="sr-only">Envío</legend>
            <p class="text-[20px] font-bold leading-[1.5] text-[#17181b]">Envio</p>
            @foreach (Pedido::ENVIOS as $clave => [$texto, $precio])
                <label class="mt-[12px] flex cursor-pointer items-start gap-[6px]">
                    <input type="radio" name="envio" value="{{ $clave }}" @checked($envioElegido === $clave) class="caja-check peer sr-only">
                    <span class="check mt-[3px] flex size-[18px] shrink-0 items-center justify-center rounded-[2px] border border-linea"></span>
                    <span class="flex-1">
                        <span class="flex justify-between gap-2 text-[16px] leading-[1.5] text-[#17181b]">{{ $texto }} <b class="text-[14px] font-bold">{{ $precio }}</b></span>
                        @if ($clave === 'retiro_local' && $info?->retiro_texto)
                            <span class="mt-[6px] block whitespace-pre-line text-[14px] leading-[1.5] text-[#969696]">{{ $info->retiro_texto }}</span>
                        @endif
                    </span>
                </label>
            @endforeach
        </fieldset>

        <div class="flex items-start justify-between border-b border-linea py-[22px]">
            <p class="text-[20px] font-bold leading-[1.5] text-[#17181b]">Total</p>
            <div class="text-right">
                <p class="text-[24px] font-bold leading-[1.2] text-[#17181b]">{{ Producto::formatearPrecio($totales['total']) }}</p>
                @if ($totales['muestra_iva'])
                    <p class="text-[14px] leading-[1.5] text-[#17181b]/60">{{ Producto::formatearPrecio($totales['subtotal']) }} sin IVA</p>
                @endif
            </div>
        </div>

        <div class="py-[22px]">
            <button type="submit" @disabled($totales['total'] <= 0)
                    class="w-full rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d] disabled:cursor-not-allowed disabled:opacity-50">
                Finalizar compra
            </button>
        </div>
    </form>
</div>
