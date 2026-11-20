@extends('layouts.post-venta-formulario', [
    'titulo' => 'Registrá tu producto',
    'clave' => 'registro',
    'accion' => route('post-venta.registro.enviar'),
])

@section('campos')
    @include('components.campo-formulario', ['nombre' => 'modelo', 'label' => 'Modelo*', 'placeholder' => 'Ej. Serie 300', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'numero_serie', 'label' => 'Número de serie*', 'placeholder' => '0000000000', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'email', 'label' => 'E-mail*', 'placeholder' => 'juanperez@mail.com', 'tipo' => 'email', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'celular', 'label' => 'Celular', 'placeholder' => 'Código de área + 1234 5678', 'tipo' => 'tel'])
    @include('components.campo-formulario', ['nombre' => 'nombre', 'label' => 'Nombre*', 'placeholder' => 'Juan', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'apellido', 'label' => 'Apellido*', 'placeholder' => 'Perez', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'empresa', 'label' => 'Empresa*', 'placeholder' => 'Nombre de la empresa / Razón social', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'rubro', 'label' => 'Rubro*', 'placeholder' => 'Ej. Alimenticia', 'requerido' => true])
@endsection

@section('boton', 'Registrar producto')
