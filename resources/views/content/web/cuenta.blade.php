@extends('layouts.web')

@section('title', 'Mi cuenta · Dixter')

@php
    $usuario = auth('web_usuario')->user();
    $input = 'mt-[3px] h-[44px] w-full rounded-[4px] border border-linea bg-white/95 px-[16px] text-[14px] placeholder:text-gris/50 focus:border-marca focus:outline-none';
    $caja = 'rounded-[4px] bg-white px-[19px] pb-[32px] pt-[34px] shadow-[0_0_10px_rgba(0,0,0,0.2)] md:px-[24px]';
@endphp

@section('content')
<div class="mx-auto max-w-[1366px] px-4 pb-[100px] md:px-[71px]">
    {{-- Migas --}}
    <p class="pt-[24px] text-[12px] leading-[1.5]"><a href="{{ route('home') }}" class="font-bold">Inicio</a> &gt; Mi cuenta</p>

    @if ($usuario)
        {{-- ===== Sesión iniciada ===== --}}
        <h1 class="mt-[70px] text-[36px] font-semibold leading-[1.2] md:text-[48px]">Hola, {{ $usuario->usuario }}</h1>

        @php
            $pedidos = $usuario->pedidos()->withCount('items')->latest()->take(20)->get();
            $favoritosUsuario = $usuario->favoritos()->with('fotos')->where('active', true)->get();
        @endphp

        <div class="mt-[40px] grid grid-cols-1 items-start gap-[24px] lg:grid-cols-[288px_1fr]">
            <div class="{{ $caja }}">
                <h2 class="text-[24px] font-semibold leading-[1.2] text-marca">Mis datos</h2>
                <dl class="mt-[18px] space-y-[10px] text-[16px] leading-[1.5]">
                    <div><dt class="text-[14px] text-black/60">Email</dt><dd class="break-all">{{ $usuario->email }}</dd></div>
                    @if ($usuario->telefono)<div><dt class="text-[14px] text-black/60">Teléfono</dt><dd>{{ $usuario->telefono }}</dd></div>@endif
                    @if ($usuario->empresa)<div><dt class="text-[14px] text-black/60">Empresa</dt><dd>{{ $usuario->empresa }}</dd></div>@endif
                </dl>
                <form method="POST" action="{{ route('web.logout') }}" class="mt-[24px]">
                    @csrf
                    <button type="submit" class="w-full rounded-[4px] border border-marca bg-white p-[10px] text-[16px] leading-[1.5] text-marca transition hover:bg-marca hover:text-white">Cerrar sesión</button>
                </form>
            </div>

            {{-- Mis pedidos --}}
            <section id="pedidos" class="rounded-[4px] border border-linea" aria-labelledby="titulo-pedidos">
                <h2 id="titulo-pedidos" class="border-b border-linea px-[22px] py-[18px] text-[24px] font-semibold leading-[1.2]">Mis pedidos</h2>
                @if ($pedidos->isEmpty())
                    <p class="px-[22px] py-[32px] text-[16px]">Todavía no hiciste pedidos.
                        <a href="{{ route('productos') }}" class="font-semibold text-marca underline">Ver productos</a></p>
                @else
                    <ul>
                        @foreach ($pedidos as $pedido)
                            <li class="flex flex-col gap-[8px] border-b border-linea px-[22px] py-[18px] last:border-b-0 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-[16px] font-bold">{{ $pedido->numero() }} <span class="font-normal text-black/60">· {{ $pedido->created_at->format('d/m/Y') }}</span></p>
                                    <p class="text-[14px] text-black/70">{{ $pedido->items_count }} {{ $pedido->items_count === 1 ? 'producto' : 'productos' }} · {{ $pedido->envio_texto }} · {{ $pedido->pago_texto }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-[12px] md:justify-end">
                                    <span class="rounded-[4px] bg-[#f5f5f5] px-[10px] py-[4px] text-[13px]">{{ $pedido->estado_texto }}</span>
                                    @if ($pedido->pago_estado)
                                        <span class="rounded-[4px] px-[10px] py-[4px] text-[13px] {{ $pedido->pago_estado === 'aprobado' ? 'bg-green-100 text-green-800' : 'bg-marca/10 text-marca' }}">{{ $pedido->pago_estado_texto }}</span>
                                    @endif
                                    <span class="text-[20px] font-extrabold">{{ \App\Models\Producto::formatearPrecio($pedido->total) }}</span>
                                    @if ($pedido->puede_pagar_mercado_pago)
                                        <a href="{{ route('compra.mp.pagar', $pedido) }}" class="rounded-[4px] bg-marca px-[14px] py-[6px] text-[14px] text-white hover:bg-[#a8231d]">Pagar</a>
                                    @endif
                                    {{-- Solo "En espera": después hay que comunicarse con Dixter --}}
                                    @if ($pedido->cancelable_por_cliente)
                                        <form method="POST" action="{{ route('pedido.cancelar', $pedido) }}" data-cancelar-pedido="{{ $pedido->numero() }}">
                                            @csrf
                                            <button type="submit" class="rounded-[4px] border border-marca px-[14px] py-[6px] text-[14px] text-marca transition hover:bg-marca hover:text-white">Cancelar pedido</button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        {{-- Favoritos --}}
        @if ($favoritosUsuario->isNotEmpty())
            <section class="mt-[60px]" aria-labelledby="titulo-favoritos">
                <h2 id="titulo-favoritos" class="text-[28px] font-semibold leading-[1.2] md:text-[32px]">Mis favoritos</h2>
                <div class="mt-[24px] grid grid-cols-1 gap-[24px] sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($favoritosUsuario as $producto)
                        @include('components.producto-card', ['producto' => $producto, 'favoritos' => $favoritosUsuario->pluck('id')->all()])
                    @endforeach
                </div>
            </section>
        @endif
    @else
        {{-- ===== Login + registro ===== --}}
        <h1 class="mt-[70px] text-[36px] font-semibold leading-[1.2] md:text-[48px]">Mi cuenta</h1>
        <p class="mt-[12px] max-w-[640px] text-[16px] leading-[1.5]">Iniciá sesión o creá tu cuenta para comprar online y seguir tus pedidos.</p>
        @if (session('aviso_login'))
            <p class="mt-[20px] max-w-[640px] rounded-[4px] border border-marca/30 bg-marca/5 px-[16px] py-[12px] text-[16px] text-marca" role="status">{{ session('aviso_login') }}</p>
        @endif

        <div class="mt-[40px] grid grid-cols-1 items-start gap-[24px] lg:grid-cols-[367px_1fr]">
            {{-- Iniciar sesión (Figma: Ingreso) --}}
            <div class="{{ $caja }}">
                <h2 class="text-[24px] font-semibold leading-[1.2] text-marca">Iniciar sesión</h2>

                <form id="form-web-login" class="mt-[18px]" data-accion="{{ route('web.login') }}" novalidate>
                    @csrf
                    <label for="login-usuario" class="block text-[16px] leading-[1.5]">Usuario o email</label>
                    <input id="login-usuario" name="usuario" type="text" autocomplete="username" required placeholder="juanperez@mail.com" class="{{ $input }}">

                    <label for="login-contrasena" class="mt-[16px] block text-[16px] leading-[1.5]">Contraseña</label>
                    <input id="login-contrasena" name="contraseña" type="password" autocomplete="current-password" required placeholder="*****" class="{{ $input }}">

                    <p class="form-error mt-[12px] hidden text-[14px] text-marca" role="alert"></p>

                    <button type="submit" class="mt-[26px] w-full rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d] disabled:opacity-60">Ingresar</button>
                </form>

                <div class="mt-[24px] border-t border-linea pt-[18px] text-center text-[14px] leading-[1.5]">
                    ¿No tenés usuario?
                    <a href="#registro" data-ir-registro class="block text-[16px] font-semibold underline">Registrate</a>
                </div>
            </div>

            {{-- Crear cuenta --}}
            <div id="registro" class="{{ $caja }} scroll-mt-[24px]">
                <h2 class="text-[24px] font-semibold leading-[1.2] text-marca">Crear cuenta</h2>

                <form id="form-web-registro" class="mt-[18px] grid grid-cols-1 gap-x-[22px] gap-y-[16px] md:grid-cols-2" data-accion="{{ route('web.register') }}" novalidate>
                    @csrf
                    <div>
                        <label for="reg-nombre" class="block text-[16px] leading-[1.5]">Nombre y apellido*</label>
                        <input id="reg-nombre" name="usuario" type="text" autocomplete="name" required placeholder="Juan Perez" class="{{ $input }}">
                    </div>
                    <div>
                        <label for="reg-email" class="block text-[16px] leading-[1.5]">E-mail*</label>
                        <input id="reg-email" name="email" type="email" autocomplete="email" required placeholder="juanperez@mail.com" class="{{ $input }}">
                    </div>
                    <div>
                        <label for="reg-contrasena" class="block text-[16px] leading-[1.5]">Contraseña*</label>
                        <input id="reg-contrasena" name="contraseña" type="password" autocomplete="new-password" required placeholder="Mínimo 8 caracteres" class="{{ $input }}">
                    </div>
                    <div>
                        <label for="reg-contrasena2" class="block text-[16px] leading-[1.5]">Repetir contraseña*</label>
                        <input id="reg-contrasena2" name="contraseña_confirmation" type="password" autocomplete="new-password" required class="{{ $input }}">
                    </div>
                    <div>
                        <label for="reg-telefono" class="block text-[16px] leading-[1.5]">Celular</label>
                        <input id="reg-telefono" name="telefono" type="tel" autocomplete="tel" placeholder="Código de área + 1234 5678" class="{{ $input }}">
                    </div>
                    <div>
                        <label for="reg-empresa" class="block text-[16px] leading-[1.5]">Empresa / Razón social</label>
                        <input id="reg-empresa" name="empresa" type="text" autocomplete="organization" class="{{ $input }}">
                    </div>

                    <p class="form-error hidden text-[14px] text-marca md:col-span-2" role="alert"></p>

                    <div class="flex flex-col gap-[12px] md:col-span-2 md:flex-row md:items-center md:justify-between">
                        <p class="text-[14px] leading-[1.5]">*Datos obligatorios</p>
                        <button type="submit" class="w-full rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d] disabled:opacity-60 md:w-[288px]">Crear cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cancelar pedido (solo "En espera"): confirmación y resultado con SweetAlert
    document.querySelectorAll('form[data-cancelar-pedido]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.dataset.confirmado) return;
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: '¿Cancelar el pedido ' + form.dataset.cancelarPedido + '?',
                text: 'Te vamos a enviar un mail con el detalle. Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar pedido',
                cancelButtonText: 'Volver',
                confirmButtonColor: '#C32922',
                reverseButtons: true,
            }).then(function (r) {
                if (!r.isConfirmed) return;
                form.dataset.confirmado = '1';
                form.querySelector('button').disabled = true;
                form.submit();
            });
        });
    });
    @if (session('ok_cuenta'))
        Swal.fire({ icon: 'success', title: 'Pedido cancelado', text: @json(session('ok_cuenta')), confirmButtonText: 'Entendido', confirmButtonColor: '#C32922' });
    @elseif (session('error_cuenta'))
        Swal.fire({ icon: 'error', title: 'No se pudo cancelar', text: @json(session('error_cuenta')), confirmButtonText: 'Entendido', confirmButtonColor: '#C32922' });
    @endif

    document.querySelector('[data-ir-registro]')?.addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('registro').scrollIntoView({ behavior: 'smooth' });
        setTimeout(() => document.getElementById('reg-nombre')?.focus(), 400);
    });

    // Login y registro por AJAX (AuthController@webLogin / webRegister devuelven JSON)
    ['form-web-login', 'form-web-registro'].forEach(function (id) {
        const form = document.getElementById(id);
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const boton = form.querySelector('button[type="submit"]');
            const error = form.querySelector('.form-error');
            boton.disabled = true;
            error.classList.add('hidden');

            fetch(form.dataset.accion, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
            .then(async (r) => ({ ok: r.ok, data: await r.json() }))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    window.location.href = data.redirect;
                    return;
                }
                error.textContent = data.message || 'Revisá los datos e intentá nuevamente.';
                error.classList.remove('hidden');
                boton.disabled = false;
            })
            .catch(() => {
                error.textContent = 'Ocurrió un error. Intentá nuevamente.';
                error.classList.remove('hidden');
                boton.disabled = false;
            });
        });
    });
});
</script>
@endpush
