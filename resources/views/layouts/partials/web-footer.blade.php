{{-- Footer del sitio (Figma: FOOTER) --}}
@php
    $c = $contacto_contenido;
    $secciones = [
        [['Productos', $link('productos')], ['Industrias', $link('aplicaciones')], ['Donde comprar', $link('nuestra-red')], ['Soporte técnico', $link('soporte')]],
        [['Post Venta', $link('post-venta')], ['Nosotros', $link('nosotros')], ['Contacto', $link('contacto')]],
    ];
@endphp

<footer class="bg-black text-white">
    <div class="mx-auto grid max-w-[1366px] grid-cols-1 gap-10 px-4 pb-[60px] pt-[60px] md:grid-cols-2 md:px-[70px] lg:pt-[79px] xl:grid-cols-[289fr_336fr_314fr_285fr] xl:gap-0">
        {{-- Logo --}}
        <div class="lg:pt-[6px]">
            <a href="{{ route('home') }}" aria-label="Dixter - Inicio">
                <img src="{{ $logoFooter }}" alt="Dixter" width="232" height="37" class="h-[37px] w-[232px] object-cover">
            </a>
        </div>

        {{-- Secciones --}}
        <div>
            <h3 class="font-dm text-[20px] font-bold leading-[1.2]">Secciones</h3>
            <div class="mt-[26px] flex gap-[47px] font-plex text-[16px]">
                @foreach ($secciones as $columna)
                    <ul class="flex flex-col">
                        @foreach ($columna as [$texto, $url])
                            <li class="leading-[1.9]"><a href="{{ $url }}" class="hover:underline">{{ $texto }}</a></li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>

        {{-- Newsletter + redes --}}
        <div>
            <h3 class="font-dm text-[20px] font-bold leading-[1.2]">Suscribite al Newsletter</h3>
            <form action="{{ route('adm.newsletter-store') }}" id="newsletterForm" method="POST"
                  class="mt-[26px] flex h-[49px] w-full max-w-[289px] items-center rounded-[4px] border border-linea">
                @csrf
                <label for="newsletterEmail" class="sr-only">Email</label>
                <input type="email" id="newsletterEmail" name="email" placeholder="Email" required
                       class="h-full min-w-0 flex-1 bg-transparent px-[12px] font-dm text-[14px] text-white placeholder:text-white focus:outline-none">
                <button type="submit" aria-label="Suscribirme" class="flex h-full w-[42px] items-center justify-center">
                    <img src="{{ asset('assets/img/dixter/icon-newsletter-arrow.svg') }}" alt="" width="24" height="24" class="size-[24px]">
                </button>
            </form>

            @if ($c?->facebook || $c?->instagram || $c?->youtube)
                <h3 class="mt-[29px] font-dm text-[20px] font-bold leading-[1.2]">Seguinos en</h3>
                <div class="mt-[9px] flex gap-[8px]">
                    @if ($c->facebook)
                        <a href="{{ $c->facebook }}" target="_blank" rel="noopener" aria-label="Facebook"><img src="{{ asset('assets/img/dixter/icon-facebook.svg') }}" alt="" width="16" height="16"></a>
                    @endif
                    @if ($c->instagram)
                        <a href="{{ $c->instagram }}" target="_blank" rel="noopener" aria-label="Instagram"><img src="{{ asset('assets/img/dixter/icon-instagram.svg') }}" alt="" width="16" height="16"></a>
                    @endif
                    @if ($c->youtube)
                        <a href="{{ $c->youtube }}" target="_blank" rel="noopener" aria-label="YouTube"><img src="{{ asset('assets/img/dixter/icon-youtube.svg') }}" alt="" width="16" height="16"></a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Contacto --}}
        <div>
            <h3 class="font-dm text-[20px] font-bold leading-[1.2]">Contacto</h3>
            <ul class="mt-[22px] flex flex-col gap-[18px] font-plex text-[16px] leading-[1.5]">
                @if ($c?->direccion)
                    <li>
                        <a href="https://maps.google.com/?q={{ urlencode($c->direccion) }}" target="_blank" rel="noopener" class="flex items-start gap-[11px] hover:underline">
                            <img src="{{ asset('assets/img/dixter/icon-map-pin.svg') }}" alt="" width="20" height="20" class="mt-[2px] shrink-0">
                            <span class="max-w-[230px]">{!! nl2br(e($c->direccion)) !!}</span>
                        </a>
                    </li>
                @endif
                @if ($c?->telefono)
                    <li>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $c->telefono) }}" class="flex items-center gap-[11px] hover:underline">
                            <img src="{{ asset('assets/img/dixter/icon-phone.svg') }}" alt="" width="20" height="20" class="shrink-0">
                            <span>{{ $c->telefono }}</span>
                        </a>
                    </li>
                @endif
                @if ($c?->mail)
                    <li>
                        <a href="mailto:{{ $c->mail }}" class="flex items-center gap-[11px] hover:underline">
                            <img src="{{ asset('assets/img/dixter/icon-mail.svg') }}" alt="" width="20" height="20" class="shrink-0">
                            <span>{{ $c->mail }}</span>
                        </a>
                    </li>
                @endif
                @if ($c?->whatssap)
                    <li>
                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $c->whatssap) }}" target="_blank" rel="noopener" class="flex items-center gap-[11px] hover:underline">
                            <img src="{{ asset('assets/img/dixter/icon-whatsapp-red.svg') }}" alt="" width="20" height="20" class="shrink-0">
                            <span>{{ $c->whatssap }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    {{-- Barra inferior --}}
    <div class="bg-[#121212]">
        <div class="mx-auto flex max-w-[1366px] flex-col gap-2 px-4 py-[22px] font-dm text-[14px] leading-[1.5] md:flex-row md:items-center md:justify-between md:px-[74px]">
            <p>© Copyright {{ date('Y') }} Dixter. Equipos y Productos de limpieza. Todos los derechos reservados</p>
            <a href="https://osole.com.ar/" target="_blank" rel="noopener" class="hover:underline">By Osole</a>
        </div>
    </div>
</footer>
