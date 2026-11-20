{{--
    Migas + etiqueta + título de las páginas internas (Figma: "SOPORTE TÉCNICO / Tu equipo, siempre en marcha.").
    $migas: [['Post Venta', url], ['Registrá tu producto', null]]  (Inicio va siempre primero)
    $etiqueta, $titulo
--}}
<p class="pt-[24px] text-[12px] leading-[1.5]">
    <a href="{{ route('home') }}" class="font-bold">Inicio</a>
    @foreach ($migas as [$texto, $url])
        &gt; @if ($url)<a href="{{ $url }}" class="font-bold">{{ $texto }}</a>@else{{ $texto }}@endif
    @endforeach
</p>

<div class="mt-[50px] md:mt-[68px]">
    @if (!empty($etiqueta))
        <p class="text-[14px] font-bold uppercase leading-[1.5]">{{ $etiqueta }}</p>
    @endif
    <h1 class="text-[34px] font-semibold leading-[1.2] md:text-[48px]">{{ $titulo }}</h1>
</div>
