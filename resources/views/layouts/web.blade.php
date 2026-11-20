@php
    $contacto_contenido = App\Models\Contacto::first();
    $logos = App\Models\Logo::first();
    $logoNav = $logos->foto_nav ?? asset('assets/img/dixter/logo.png');
    $logoFooter = $logos->foto_footer ?? asset('assets/img/dixter/logo.png');

    // Variante del header: 'default' (blanco), 'transparente' (sobre el hero) o 'clientes' (Área clientes)
    $header = $header ?? 'default';

    $currentRoute = request()->route() ? request()->route()->getName() : null;
    $metadato = \App\Models\Metadato::where('seccion', $currentRoute)->first();

    // Links a secciones que se van armando por etapas: si la ruta todavía no existe, queda en "#"
    $link = fn (string $ruta) => Route::has($ruta) ? route($ruta) : '#';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dixter · Equipos y productos de limpieza')</title>

    <meta name="description" content="{{ $metadato->descripcion ?? 'Dixter: aspiradoras, lavadoras, pulidoras y turbinas profesionales. Fabricando en Argentina desde 1947.' }}">
    <meta name="keywords" content="{{ $metadato->keywords ?? 'Dixter, aspiradoras industriales, lavadoras de pisos, pulidoras, turbinas, secadores' }}">

    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,300;6..12,400;6..12,600;6..12,700;6..12,800&family=DM+Sans:opsz,wght@9..40,400;9..40,700&family=IBM+Plex+Sans:wght@400&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Nunito Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        dm: ['"DM Sans"', 'sans-serif'],
                        plex: ['"IBM Plex Sans"', 'sans-serif'],
                    },
                    colors: {
                        marca: '#C32922',     // Brown
                        linea: '#DEDFE0',     // Line
                        gris: '#ABACB0',      // Grey
                        oscuro: '#121212',
                    },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <style>
        body { font-family: "Nunito Sans", ui-sans-serif, system-ui, sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-white text-black antialiased">

    {{-- ===================== HEADER ===================== --}}
    @include('layouts.partials.web-header')

    {{-- ===================== CONTENIDO ===================== --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ===================== FOOTER ===================== --}}
    @include('layouts.partials.web-footer')

    {{-- WhatsApp flotante --}}
    @if (!empty($contacto_contenido?->whatssap))
        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $contacto_contenido->whatssap) }}" target="_blank" rel="noopener"
           aria-label="Escribinos por WhatsApp"
           class="fixed bottom-[24px] right-[16px] md:bottom-[67px] md:right-[34px] z-[900] size-[60px] md:size-[75px] transition-transform hover:scale-105">
            <img src="{{ asset('assets/img/dixter/whatsapp-float.svg') }}" alt="" width="75" height="75" class="size-full">
        </a>
    @endif

    {{-- Modal de login del Área clientes (sin registro) --}}
    @include('components.modal-area-clientes')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Favoritos, carrito y mini carrito --}}
    @include('layouts.partials.tienda-scripts')

    {{-- Menú mobile --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('menu-toggle');
            const menu = document.getElementById('mobile-menu');
            toggle?.addEventListener('click', () => {
                const abierto = menu.classList.toggle('hidden') === false;
                toggle.setAttribute('aria-expanded', abierto);
            });
        });
    </script>

    {{-- Newsletter --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('newsletterForm');
            if (!form) return;

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const boton = this.querySelector('button[type="submit"]');
                boton.disabled = true;

                fetch(this.action, { method: 'POST', body: new FormData(this), headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.success ? 'success' : 'error',
                            title: data.success ? '¡Suscripción exitosa!' : 'No pudimos suscribirte',
                            text: data.message,
                            confirmButtonColor: '#C32922',
                        });
                        if (data.success) form.reset();
                    })
                    .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un error. Intentá nuevamente.', confirmButtonColor: '#C32922' }))
                    .finally(() => { boton.disabled = false; });
            });
        });
    </script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>
