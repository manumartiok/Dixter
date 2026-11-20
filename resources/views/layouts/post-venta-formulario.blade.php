{{--
    Base de los formularios de Post Venta (Figma: Post Venta A / B / C):
    formulario a la izquierda y las otras tarjetas de Post Venta a la derecha.
    Las vistas definen: $titulo, $clave (tarjeta a ocultar), $accion (ruta del POST) y la sección "campos".
--}}
@extends('layouts.web')

@section('title', $titulo . ' · Post Venta · Dixter')

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[100px] md:px-[71px]">
    @include('components.encabezado-pagina', [
        'migas' => [['Post Venta', route('post-venta')], [$titulo, null]],
        'etiqueta' => 'Post Venta',
        'titulo' => $titulo,
    ])

    <div class="mt-[40px] grid grid-cols-1 gap-[60px] lg:mt-[51px] lg:grid-cols-[1fr_289px] lg:gap-x-[127px]">
        <div>
            @if (session('ok_formulario'))
                <div class="mb-[24px] rounded-[4px] border border-green-200 bg-green-50 px-[16px] py-[12px] text-[16px] text-green-800" role="status">{{ session('ok_formulario') }}</div>
            @endif
            @if (session('error_formulario'))
                <div class="mb-[24px] rounded-[4px] border border-marca/30 bg-marca/5 px-[16px] py-[12px] text-[16px] text-marca" role="alert">{{ session('error_formulario') }}</div>
            @endif

            <form method="POST" action="{{ $accion }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-x-[24px] gap-y-[24px] md:grid-cols-2" data-formulario-postventa>
                @csrf
                @yield('campos')

                <div class="md:col-start-2">
                    <button type="submit" class="mt-[16px] w-full rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d] disabled:opacity-60">
                        @yield('boton')
                    </button>
                </div>
            </form>

            @yield('despues')
        </div>

        <aside>
            @include('components.postventa-tarjetas', ['excluir' => $clave, 'vertical' => true])
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Evita envíos dobles
    document.querySelector('[data-formulario-postventa]')?.addEventListener('submit', function () {
        const boton = this.querySelector('[type=submit]');
        setTimeout(() => { boton.disabled = true; }, 0);
    });
</script>
@endpush
