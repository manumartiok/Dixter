{{--
    Modal "Iniciar sesión" del Área clientes (Figma: Ingreso).
    Sin registro: los clientes se crean desde el administrador.
    Se abre con cualquier elemento [data-abrir-modal-clientes].
--}}
@guest('cliente')
<div id="modal-clientes" class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/60 px-4"
     role="dialog" aria-modal="true" aria-labelledby="modal-clientes-titulo">
    <div class="modal-clientes-caja relative w-full max-w-[367px] rounded-[4px] bg-white px-[19px] pb-[32px] pt-[34px] shadow-[0_0_10px_rgba(0,0,0,0.2)]">
        <button type="button" data-cerrar-modal-clientes aria-label="Cerrar"
                class="absolute right-[14px] top-[12px] text-[22px] leading-none text-black/40 hover:text-black">&times;</button>

        <h2 id="modal-clientes-titulo" class="text-[24px] font-semibold leading-[1.2] text-marca">Iniciar sesión</h2>

        <form id="form-login-clientes" class="mt-[18px]" novalidate>
            @csrf
            <label for="cliente-usuario" class="block text-[16px] leading-[1.5]">Usuario</label>
            <input id="cliente-usuario" name="usuario" type="text" autocomplete="username" required placeholder="Nombre o mail"
                   class="mt-[3px] h-[44px] w-full rounded-[4px] border border-linea bg-white/95 px-[16px] text-[14px] placeholder:text-gris/50 focus:border-marca focus:outline-none">

            <label for="cliente-contrasena" class="mt-[16px] block text-[16px] leading-[1.5]">Contraseña</label>
            <input id="cliente-contrasena" name="contraseña" type="password" autocomplete="current-password" required placeholder="*****"
                   class="mt-[3px] h-[44px] w-full rounded-[4px] border border-linea bg-white/95 px-[16px] text-[14px] placeholder:text-gris/50 focus:border-marca focus:outline-none">

            <button type="submit"
                    class="mt-[26px] w-full rounded-[4px] bg-marca p-[10px] text-[16px] leading-[1.5] text-white transition hover:bg-[#a8231d] disabled:opacity-60">
                Ingresar
            </button>
        </form>

        <div class="mt-[24px] border-t border-linea pt-[18px] text-center text-[14px] leading-[1.5]">
            ¿Sos cliente y todavía no tenés usuario?
            <a href="{{ Route::has('contacto') ? route('contacto') : '#' }}" class="block font-semibold underline">Contactanos</a>
        </div>
    </div>
</div>
@endguest

<style>
    #modal-clientes.flex { animation: modalClientesFade .2s ease-out; }
    #modal-clientes.flex .modal-clientes-caja { animation: modalClientesSlide .25s ease-out; }
    @keyframes modalClientesFade { from { opacity: 0; } to { opacity: 1; } }
    @keyframes modalClientesSlide { from { opacity: 0; transform: translateY(-16px); } to { opacity: 1; transform: none; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Dropdown del cliente logueado (header del Área clientes)
    const btnMenu = document.getElementById('btn-menu-cliente');
    const dropdown = document.getElementById('dropdown-cliente');
    if (btnMenu && dropdown) {
        btnMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            const abierto = dropdown.classList.toggle('hidden') === false;
            btnMenu.setAttribute('aria-expanded', abierto);
        });
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) dropdown.classList.add('hidden');
        });
    }

    const modal = document.getElementById('modal-clientes');
    if (!modal) return;

    const form = document.getElementById('form-login-clientes');
    const alerta = (icon, title, text) => window.Swal
        ? Swal.fire({ icon, title, text, confirmButtonColor: '#C32922', confirmButtonText: 'Entendido' })
        : alert(text);

    const abrir = () => {
        document.getElementById('mobile-menu')?.classList.add('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('cliente-usuario')?.focus(), 50);
    };
    const cerrar = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };

    document.querySelectorAll('[data-abrir-modal-clientes]').forEach(b => b.addEventListener('click', abrir));
    modal.querySelectorAll('[data-cerrar-modal-clientes]').forEach(b => b.addEventListener('click', cerrar));
    modal.addEventListener('click', (e) => { if (e.target === modal) cerrar(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrar(); });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const boton = form.querySelector('button[type="submit"]');
        boton.disabled = true;

        fetch('{{ route('cliente.login') }}', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        })
        .then(async (r) => ({ ok: r.ok, data: await r.json() }))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                cerrar();
                const ir = () => { window.location.href = data.redirect; };
                window.Swal
                    ? Swal.fire({ icon: 'success', title: '¡Bienvenido!', text: 'Ingresaste al Área clientes.', timer: 1300, showConfirmButton: false }).then(ir)
                    : ir();
                return;
            }
            // 422 de validación o credenciales: Laravel y el controlador mandan el texto en "message"
            alerta('error', 'No pudimos iniciar sesión', data.message || 'Revisá los datos e intentá nuevamente.');
            boton.disabled = false;
        })
        .catch(() => {
            alerta('error', 'Error', 'Ocurrió un error. Intentá nuevamente.');
            boton.disabled = false;
        });
    });

    @if (session('abrir_modal_clientes'))
        abrir();
    @endif
});
</script>
