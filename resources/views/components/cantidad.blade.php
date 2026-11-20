{{-- Selector de cantidad (Figma: − 1 +). $productoId, $cantidad, $ancho (px, opcional) --}}
<div class="cantidad flex h-[44px] shrink-0 items-center justify-between rounded-[2px] border border-linea bg-white px-[16px]" style="width: {{ $ancho ?? 138 }}px"
     data-cantidad-producto="{{ $productoId }}">
    <button type="button" data-restar aria-label="Restar uno"><img src="{{ asset('assets/img/dixter/icon-minus.svg') }}" alt="" width="16" height="16"></button>
    <input type="number" min="1" max="99" value="{{ $cantidad }}" inputmode="numeric" aria-label="Cantidad"
           class="w-[44px] bg-transparent text-center text-[20px] leading-[1.5] text-[#17181b] [appearance:textfield] focus:outline-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
    <button type="button" data-sumar aria-label="Sumar uno"><img src="{{ asset('assets/img/dixter/icon-plus.svg') }}" alt="" width="16" height="16"></button>
</div>
