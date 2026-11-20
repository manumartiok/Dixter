@extends('layouts.post-venta-formulario', [
    'titulo' => 'Arrepentimiento de compra',
    'clave' => 'arrepentimiento',
    'accion' => route('post-venta.arrepentimiento.enviar'),
])

@section('campos')
    @include('components.campo-formulario', ['nombre' => 'nombre', 'label' => 'Nombre*', 'placeholder' => 'Juan', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'apellido', 'label' => 'Apellido*', 'placeholder' => 'Perez', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'email', 'label' => 'Email*', 'placeholder' => 'juanperez@mail.com', 'tipo' => 'email', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'celular', 'label' => 'Celular', 'placeholder' => 'Código de área + 1234 5678', 'tipo' => 'tel'])
    @include('components.campo-formulario', ['nombre' => 'numero_pedido', 'label' => 'Número de pedido*', 'placeholder' => 'Nº', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'fecha_compra', 'label' => 'Fecha de compra*', 'tipo' => 'date', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'motivo', 'label' => 'Motivo de la devolución', 'placeholder' => 'Describir motivo'])
    @include('components.campo-formulario', ['nombre' => 'adjunto', 'label' => 'Adjuntar archivo', 'tipo' => 'file', 'placeholder' => 'Seleccionar archivo', 'accept' => '.jpg,.jpeg,.png,.webp,.pdf,.doc,.docx'])
    @include('components.campo-formulario', ['nombre' => 'descripcion', 'label' => 'Describa el problema con su pedido', 'tipo' => 'textarea', 'placeholder' => 'Describa detalladamente el problema que ha tenido con el producto', 'filas' => 7, 'clase' => 'md:col-span-2'])
@endsection

@section('boton', 'Enviar arrepentimiento')

@section('despues')
    <div class="mt-[64px] rounded-[4px] border border-linea bg-gris/5 px-[15px] py-[18px] text-[16px]">
        <p class="font-bold leading-[1.5]">Importante.</p>
        <ul class="mt-[4px] space-y-[2px]">
            @foreach ([
                'Recordá que tenés tiempo para devolver el producto hasta 10 días después de tu compra.',
                'El pedido debe ser devuelto en su embalaje original y sin usar.',
                'Los campos con el signo “*” son obligatorios',
            ] as $aviso)
                <li class="flex items-start gap-[8px] leading-[1.8]">
                    <img src="{{ asset('assets/img/dixter/icon-circle-check.svg') }}" alt="" width="16" height="16" class="mt-[6px] shrink-0">
                    {{ $aviso }}
                </li>
            @endforeach
        </ul>
    </div>
@endsection
