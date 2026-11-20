{{--
    Comportamiento compartido de la tienda:
    - [data-favorito]           corazón de productos
    - form[data-agregar-carrito] agregar al carrito (abre el mini carrito)
    - .cantidad [data-restar]/[data-sumar] selectores de cantidad
    - [data-quitar], [data-cantidad-producto] en el mini carrito y la página del carrito
    Sin sesión, el servidor responde 401 y se redirige a "Mi cuenta" (login / registro).
--}}
<div id="mini-carrito" class="fixed inset-0 z-[950] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40" data-cerrar-mini></div>
    <aside role="dialog" aria-modal="true" aria-label="Carrito"
           class="mini-carrito-panel absolute right-0 top-0 max-h-screen w-full max-w-[493px] overflow-y-auto rounded-l-[4px] bg-white px-[30px] pb-[30px] pt-[30px] shadow-[0_0_10px_rgba(0,0,0,0.2)]">
        <div id="mini-carrito-contenido"></div>
    </aside>
</div>

<style>
    .favorito { color: #000; }
    .favorito.activo { color: #C32922; }
    .favorito.activo .corazon path { fill: #C32922; }
    /* Checkbox cuadrado del Figma (radios de envío / pago y términos) */
    .caja-check:checked + .check { background: #C32922; border-color: #C32922; }
    .caja-check:checked + .check::after { content: ""; width: 12px; height: 12px; background: url('{{ asset('assets/img/dixter/icon-check.svg') }}') center / contain no-repeat; }
    .caja-check:focus-visible + .check { outline: 2px solid #C32922; outline-offset: 2px; }
    #mini-carrito.abierto { display: block; }
    #mini-carrito.abierto .mini-carrito-panel { animation: miniCarritoEntrar .25s ease-out; }
    @keyframes miniCarritoEntrar { from { transform: translateX(40px); opacity: 0; } to { transform: none; opacity: 1; } }
</style>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    async function pedir(url, datos, metodo = 'POST') {
        const opciones = { method: metodo, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf } };
        if (datos) opciones.body = datos instanceof FormData ? datos : new URLSearchParams(datos);
        const r = await fetch(url, opciones);
        const data = await r.json().catch(() => ({}));
        if (r.status === 401) {               // sin sesión: a Mi cuenta (vuelve acá después de entrar)
            window.location.href = data.redirect || '{{ route('web.cuenta') }}';
            throw new Error('sin-sesion');
        }
        if (!r.ok || data.success === false) {
            Swal.fire({ icon: 'error', title: 'No pudimos completar la acción', text: data.message || 'Intentá nuevamente.', confirmButtonColor: '#C32922' });
            throw new Error('error');
        }
        return data;
    }

    function aplicar(data) {
        document.querySelectorAll('[data-contador-carrito]').forEach((c) => c.textContent = data.cantidad);
        const mini = document.getElementById('mini-carrito-contenido');
        if (mini && data.mini !== undefined) mini.innerHTML = data.mini;
        // Página del carrito: refresca líneas y resumen
        const lineas = document.getElementById('carrito-lineas');
        if (lineas && data.lineasHtml !== undefined) lineas.innerHTML = data.lineasHtml;
        const resumen = document.getElementById('carrito-resumen');
        if (resumen && data.resumen !== undefined) resumen.innerHTML = data.resumen;
    }

    const panel = document.getElementById('mini-carrito');
    const abrirMini = () => { panel.classList.add('abierto'); panel.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; };
    const cerrarMini = () => { panel.classList.remove('abierto'); panel.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; };
    window.dixterCarrito = { pedir, aplicar, abrirMini };

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrarMini(); });

    document.addEventListener('click', async (e) => {
        if (e.target.closest('[data-cerrar-mini]')) { cerrarMini(); return; }

        // Favoritos
        const fav = e.target.closest('[data-favorito]');
        if (fav) {
            e.preventDefault();
            try {
                const data = await pedir('{{ url('/favoritos') }}/' + fav.dataset.favorito);
                document.querySelectorAll('[data-favorito="' + fav.dataset.favorito + '"]').forEach((b) => {
                    b.classList.toggle('activo', data.favorito);
                    b.setAttribute('aria-pressed', data.favorito);
                    b.setAttribute('aria-label', data.favorito ? 'Quitar de favoritos' : 'Agregar a favoritos');
                });
            } catch (_) {}
            return;
        }

        // Selectores de cantidad
        const boton = e.target.closest('.cantidad [data-restar], .cantidad [data-sumar]');
        if (boton) {
            const input = boton.closest('.cantidad').querySelector('input');
            const valor = Math.min(99, Math.max(1, (parseInt(input.value) || 1) + (boton.hasAttribute('data-sumar') ? 1 : -1)));
            if (valor !== parseInt(input.value)) {
                input.value = valor;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
            return;
        }

        // Quitar del carrito
        const quitar = e.target.closest('[data-quitar]');
        if (quitar) {
            try { aplicar(await pedir('{{ route('web-carrito.quitar') }}', { producto_id: quitar.dataset.quitar })); } catch (_) {}
        }
    });

    // Cambiar cantidad dentro del carrito / mini carrito
    document.addEventListener('change', async (e) => {
        const caja = e.target.closest('[data-cantidad-producto]');
        if (!caja || e.target.tagName !== 'INPUT') return;
        const cantidad = Math.min(99, Math.max(1, parseInt(e.target.value) || 1));
        e.target.value = cantidad;
        try { aplicar(await pedir('{{ route('web-carrito.actualizar') }}', { producto_id: caja.dataset.cantidadProducto, cantidad })); } catch (_) {}
    });

    // Agregar al carrito
    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('form[data-agregar-carrito]');
        if (!form) return;
        e.preventDefault();
        const boton = form.querySelector('[type=submit]');
        boton.disabled = true;
        try {
            aplicar(await pedir('{{ route('web-carrito.agregar') }}', new FormData(form)));
            abrirMini();
        } catch (_) {
        } finally {
            boton.disabled = false;
        }
    });
})();
</script>
