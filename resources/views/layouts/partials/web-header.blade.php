{{--
    Header del sitio (Figma: HEADER). Variantes:
    - default:      fondo blanco y borde inferior.
    - transparente: sobre el hero del home, degradé negro y textos blancos.
    - clientes:     Área clientes, solo "Mis productos" + botón con el nombre del cliente.
--}}
@php
    $transparente = $header === 'transparente';
    $cliente = auth('cliente')->user();
    $cantidadCarrito = \App\Services\Carrito::delUsuarioActual()?->cantidadDeProductos() ?? 0;

    $items = [
        ['label' => 'Productos',       'url' => $link('productos'),     'activo' => request()->routeIs('productos*')],
        ['label' => 'Aplicaciones',    'url' => $link('aplicaciones'),  'activo' => request()->routeIs('aplicaciones*')],
        ['label' => 'Nuestra red',     'url' => $link('nuestra-red'),   'activo' => request()->routeIs('nuestra-red')],
        ['label' => 'Soporte técnico', 'url' => $link('soporte'),       'activo' => request()->routeIs('soporte')],
        ['label' => 'Post Venta',      'url' => $link('post-venta'),    'activo' => request()->routeIs('post-venta*')],
        ['label' => 'Nosotros',        'url' => $link('nosotros'),      'activo' => request()->routeIs('nosotros')],
        ['label' => 'Contacto',        'url' => $link('contacto'),      'activo' => request()->routeIs('contacto')],
    ];

    $colorTexto = $transparente ? 'text-white' : 'text-black';
    $colorSeparador = $transparente ? 'text-white/45' : 'text-black/30';
@endphp

<header class="{{ $transparente ? 'absolute inset-x-0 top-0 z-50' : 'relative z-50 bg-white border-b border-[#e6e6e6]' }} {{ $header === 'clientes' ? 'shadow-[0_4px_4px_rgba(0,0,0,0.05)]' : '' }}">
    @if ($transparente)
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/55 to-transparent"></div>
    @endif

    <div class="relative mx-auto flex h-[90px] max-w-[1366px] items-center gap-6 px-4 md:px-[71px] xl:px-[32px] min-[1400px]:px-[71px]">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="shrink-0" aria-label="Dixter - Inicio">
            <img src="{{ $logoNav }}" alt="Dixter" width="230" height="36" class="h-[28px] w-auto md:h-[36px] md:w-[230px] xl:h-[30px] xl:w-[192px] min-[1400px]:h-[36px] min-[1400px]:w-[230px] object-cover">
        </a>

        @if ($header === 'clientes')
            {{-- Header del Área clientes --}}
            <nav class="ml-auto flex items-center gap-6 md:gap-[34px]">
                <a href="{{ route('cliente.productos') }}" class="text-[12px] font-bold uppercase leading-[1.5] text-black">Mis productos</a>

                <div class="relative" id="menu-cliente">
                    <button type="button" id="btn-menu-cliente" aria-haspopup="true" aria-expanded="false"
                            class="min-w-[120px] rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d]">
                        {{ $cliente?->nombreCorto() }}
                    </button>
                    <div id="dropdown-cliente" class="absolute right-0 top-[calc(100%+8px)] hidden w-[220px] overflow-hidden rounded-[4px] bg-white shadow-[0_0_10px_rgba(0,0,0,0.2)]">
                        <p class="border-b border-linea px-4 py-3 text-[14px] leading-[1.4]">
                            <span class="block font-bold">{{ $cliente?->nombre }}</span>
                            @if ($cliente?->empresa)<span class="text-black/60">{{ $cliente->empresa }}</span>@endif
                        </p>
                        <form method="POST" action="{{ route('cliente.logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 text-left text-[14px] text-marca hover:bg-black/5">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </nav>
        @else
            {{-- Menú desktop --}}
            <nav class="ml-auto hidden xl:flex items-center gap-[7px] text-[12px] whitespace-nowrap" aria-label="Principal">
                @foreach ($items as $i => $item)
                    @if ($i > 0)<span class="{{ $colorSeparador }} leading-[24px]" aria-hidden="true">|</span>@endif
                    <a href="{{ $item['url'] }}"
                       class="{{ $colorTexto }} uppercase tracking-[0.3px] leading-[24px] {{ $item['activo'] ? 'font-bold' : 'font-semibold' }} hover:opacity-70 transition">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Íconos + Área clientes --}}
            <div class="ml-auto xl:ml-[19px] flex items-center gap-[19px]">
                <a href="{{ route('web.cuenta') }}" aria-label="{{ auth('web_usuario')->check() ? 'Mi cuenta' : 'Iniciar sesión o registrarse' }}"
                   class="relative size-[24px] shrink-0 hover:opacity-70 transition">
                    <img src="{{ asset($transparente ? 'assets/img/dixter/icon-user-white.svg' : 'assets/img/dixter/icon-user.svg') }}" alt="" width="24" height="24">
                    @auth('web_usuario')
                        <span class="absolute -bottom-[3px] -right-[3px] size-[9px] rounded-full bg-marca ring-2 {{ $transparente ? 'ring-transparent' : 'ring-white' }}"></span>
                    @endauth
                </a>

                <a href="{{ $link('web-carrito') }}" aria-label="Carrito ({{ $cantidadCarrito }})" class="relative size-[24px] shrink-0 hover:opacity-70 transition">
                    <img src="{{ asset($transparente ? 'assets/img/dixter/icon-cart-white.svg' : 'assets/img/dixter/icon-cart.svg') }}" alt="" width="24" height="24">
                    <span data-contador-carrito class="absolute -right-[10px] -top-[4px] flex size-[15px] items-center justify-center rounded-full bg-marca text-[11px] font-extrabold leading-none text-white">
                        {{ $cantidadCarrito }}
                    </span>
                </a>

                <span class="hidden md:inline text-[16px] leading-[1.5] {{ $colorSeparador }}" aria-hidden="true">|</span>

                @if ($cliente)
                    <a href="{{ route('cliente.productos') }}"
                       class="hidden md:flex w-[120px] items-center justify-center whitespace-nowrap rounded-[4px] border p-[10px] text-[12px] font-semibold uppercase leading-[1.5] tracking-[0.6px] transition {{ $transparente ? 'border-white text-white hover:bg-white/10' : 'border-marca bg-white text-marca hover:bg-marca hover:text-white' }}">
                        Área clientes
                    </a>
                @else
                    <button type="button" data-abrir-modal-clientes
                            class="hidden md:flex w-[120px] items-center justify-center whitespace-nowrap rounded-[4px] border p-[10px] text-[12px] font-semibold uppercase leading-[1.5] tracking-[0.6px] transition {{ $transparente ? 'border-white text-white hover:bg-white/10' : 'border-marca bg-white text-marca hover:bg-marca hover:text-white' }}">
                        Área clientes
                    </button>
                @endif

                {{-- Hamburguesa --}}
                <button id="menu-toggle" type="button" class="xl:hidden text-[22px] {{ $colorTexto }}" aria-controls="mobile-menu" aria-expanded="false" aria-label="Abrir menú">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        @endif
    </div>

    @if ($header !== 'clientes')
        {{-- Menú mobile --}}
        <div id="mobile-menu" class="hidden xl:hidden absolute inset-x-0 top-[90px] z-40 border-t border-linea bg-white shadow-md">
            <nav class="flex flex-col px-4 py-4" aria-label="Principal mobile">
                @foreach ($items as $item)
                    <a href="{{ $item['url'] }}" class="border-b border-linea py-3 text-[14px] uppercase tracking-[0.3px] {{ $item['activo'] ? 'font-bold' : 'font-semibold' }}">{{ $item['label'] }}</a>
                @endforeach
                @if ($cliente)
                    <a href="{{ route('cliente.productos') }}" class="mt-4 rounded-[4px] border border-marca p-[10px] text-center text-[12px] font-semibold uppercase tracking-[0.6px] text-marca">Área clientes</a>
                @else
                    <button type="button" data-abrir-modal-clientes class="mt-4 rounded-[4px] border border-marca p-[10px] text-[12px] font-semibold uppercase tracking-[0.6px] text-marca">Área clientes</button>
                @endif
            </nav>
        </div>
    @endif
</header>
