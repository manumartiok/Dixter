{{--
    Tarjetas de Post Venta (Figma: Component 1/3/4/5).
    $excluir: clave de la página actual (no se muestra)   $vertical: true en las subpáginas (columna derecha)
--}}
@php
    $tarjetas = [
        'registro' => ['Registrá tu producto', 'Activá la garantía y accedé a tu documentación.', 'Registrar', route('post-venta.registro')],
        'clientes' => ['Área de clientes', 'Manuales, videos, despieces y repuestos de tu equipo.', 'Ingresar', auth('cliente')->check() ? route('cliente.productos') : null],
        'sugerencias' => ['Buzón de sugerencias', 'Contanos cómo mejorar.', 'Enviar', route('post-venta.sugerencias')],
        'arrepentimiento' => ['Arrepentimiento de compra', 'Gestioná la baja de tu compra online.', 'Gestionar', route('post-venta.arrepentimiento')],
    ];
    // En las subpáginas el orden del Figma pone primero el Área de clientes
    if (!empty($vertical)) {
        $tarjetas = ['clientes' => $tarjetas['clientes']] + $tarjetas;
    }
    unset($tarjetas[$excluir ?? '']);
@endphp

<div class="{{ !empty($vertical) ? 'flex flex-col gap-[34px]' : 'grid grid-cols-1 gap-[24px] sm:grid-cols-2 lg:grid-cols-4' }}">
    @foreach ($tarjetas as [$titulo, $texto, $accion, $url])
        @php $etiquetaTag = $url ? 'a' : 'button'; @endphp
        <{{ $etiquetaTag }} @if ($url) href="{{ $url }}" @else type="button" data-abrir-modal-clientes @endif
            class="group flex min-h-[187px] flex-col border-t-[3px] border-linea pt-[23px] text-left transition hover:border-marca">
            <span class="text-[24px] font-semibold leading-[1.2] {{ $titulo === 'Arrepentimiento de compra' ? 'tracking-[-0.72px]' : '' }}">{{ $titulo }}</span>
            <span class="mt-[20px] text-[16px] leading-[1.5]">{{ $texto }}</span>
            <span class="mt-auto flex items-center gap-[10px] pt-[16px] text-[16px] font-bold leading-[1.5]">
                {{ $accion }}
                <img src="{{ asset('assets/img/dixter/icon-move-right.svg') }}" alt="" width="24" height="24" class="transition-transform group-hover:translate-x-1">
            </span>
        </{{ $etiquetaTag }}>
    @endforeach
</div>
