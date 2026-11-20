@extends('layouts.post-venta-formulario', [
    'titulo' => 'Buzón de sugerencias',
    'clave' => 'sugerencias',
    'accion' => route('post-venta.sugerencias.enviar'),
])

@section('campos')
    @include('components.campo-formulario', ['nombre' => 'nombre', 'label' => 'Nombre*', 'placeholder' => 'Juan', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'apellido', 'label' => 'Apellido*', 'placeholder' => 'Perez', 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'equipo', 'label' => '¿Qué equipo adquirió?', 'tipo' => 'select', 'placeholder' => 'Seleccionar equipo', 'opciones' => $equipos])
    @include('components.campo-formulario', ['nombre' => 'donde', 'label' => '¿Dónde realizó su compra?*', 'tipo' => 'select', 'placeholder' => 'Seleccionar modo', 'opciones' => $opciones['donde'], 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'calificacion', 'label' => 'Califique la calidad del asesoramiento que recibió durante la decisión de compra*', 'tipo' => 'select', 'placeholder' => 'Seleccionar', 'opciones' => $opciones['calificacion'], 'requerido' => true, 'clase' => 'flex flex-col justify-end'])
    @include('components.campo-formulario', ['nombre' => 'motivo', 'label' => '¿Cuál fue el motivo de compra de nuestros equipos?*', 'tipo' => 'select', 'placeholder' => 'Seleccionar motivo', 'opciones' => $opciones['motivo'], 'requerido' => true, 'clase' => 'flex flex-col justify-end'])
    @include('components.campo-formulario', ['nombre' => 'medio', 'label' => '¿Por qué medio conoció nuestra marca?*', 'tipo' => 'select', 'placeholder' => 'Seleccionar medio', 'opciones' => $opciones['medio'], 'requerido' => true])
    @include('components.campo-formulario', ['nombre' => 'recomendaria', 'label' => '¿Recomendaría nuestros productos?', 'tipo' => 'select', 'placeholder' => 'Seleccionar', 'opciones' => $opciones['recomendaria']])
    @include('components.campo-formulario', ['nombre' => 'comentario', 'label' => 'Agradeceríamos sus comentarios acerca de nuestros equipos y/o empresa para trabajar en mejorarla', 'tipo' => 'textarea', 'placeholder' => 'Dejanos un comentario', 'filas' => 6, 'clase' => 'md:col-span-2'])
@endsection

@section('boton', 'Enviar sugerencias')
