<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ProductoCliente;
use App\Models\Usuario;
use App\Models\WebUsuario;
use Database\Seeders\DixterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AreaClientesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DixterSeeder::class);
    }

    public function test_home_y_mi_cuenta_cargan(): void
    {
        $this->get('/')->assertOk()->assertSee('Equipos para profesionales.')->assertSee('Área clientes');
        $this->get('/mi-cuenta')->assertOk()->assertSee('Iniciar sesión')->assertSee('Crear cuenta');
    }

    public function test_el_modal_de_area_clientes_no_ofrece_registro(): void
    {
        $this->get('/')->assertDontSee('Registrate');
    }

    public function test_cliente_inicia_sesion_y_ve_sus_productos(): void
    {
        $this->postJson(route('cliente.login'), ['usuario' => 'marianor', 'contraseña' => 'dixter2026'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertAuthenticatedAs(Cliente::firstWhere('usuario', 'marianor'), 'cliente');

        $this->get(route('cliente.productos'))
            ->assertOk()
            ->assertSee('Mis productos')
            ->assertSee('Mariano R.')
            ->assertSee('Lavadora de pisos Super Kliner')
            ->assertSee('Manuales y documentos')
            ->assertSee('Advertencia.')
            ->assertSee('Vaciar el tambor después de cada uso')
            ->assertDontSee('Repuestos y accesorios')
            ->assertDontSee('Comprar repuestos');
    }

    public function test_login_de_cliente_con_clave_incorrecta_falla(): void
    {
        $this->postJson(route('cliente.login'), ['usuario' => 'marianor', 'contraseña' => 'mala'])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertGuest('cliente');
    }

    public function test_cliente_inactivo_no_puede_entrar(): void
    {
        Cliente::where('usuario', 'marianor')->update(['active' => false]);

        $this->postJson(route('cliente.login'), ['usuario' => 'marianor', 'contraseña' => 'dixter2026'])->assertStatus(422);
        $this->assertGuest('cliente');
    }

    public function test_sin_sesion_redirige_al_home_y_abre_el_modal(): void
    {
        $this->get(route('cliente.productos'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('abrir_modal_clientes', true);
    }

    public function test_todos_los_clientes_ven_todos_los_productos_activos(): void
    {
        $nuevo = ProductoCliente::create(['nombre' => 'Hidrolavadora nueva', 'active' => true]);
        $oculto = ProductoCliente::create(['nombre' => 'Producto oculto', 'active' => false]);
        $otro = Cliente::create(['nombre' => 'Otro cliente', 'usuario' => 'otro@example.com', 'email' => 'otro@example.com', 'contraseña' => Hash::make('x'), 'active' => true]);

        foreach ([Cliente::firstWhere('usuario', 'marianor'), $otro] as $cliente) {
            $this->actingAs($cliente, 'cliente')->get(route('cliente.productos'))->assertOk()
                ->assertSee('Hidrolavadora nueva')->assertSee('Lavadora de pisos Super Kliner')->assertDontSee('Producto oculto');
            $this->actingAs($cliente, 'cliente')->get(route('cliente.productos', $nuevo->id))->assertOk();
            $this->actingAs($cliente, 'cliente')->get(route('cliente.productos', $oculto->id))->assertNotFound();
        }
    }

    public function test_admin_crea_cliente_con_nombre_mail_y_contrasena(): void
    {
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('secreto'), 'active' => true]);
        // El alta solo pide nombre, mail y contraseña (repetida)
        $this->actingAs($admin)
            ->post(route('adm.clientes-store'), [
                'nombre' => 'Ana Gómez',
                'email' => 'ana@example.com',
                'contraseña' => 'clave123',
                'contraseña_confirmation' => 'clave123',
            ])
            ->assertRedirect(route('adm.clientes'));

        $ana = Cliente::firstWhere('email', 'ana@example.com');
        $this->assertTrue($ana->active);
        $this->assertTrue(Hash::check('clave123', $ana->contraseña));

        // Mail repetido o contraseñas distintas
        $this->actingAs($admin)->post(route('adm.clientes-store'), [
            'nombre' => 'Otra', 'email' => 'ana@example.com', 'contraseña' => 'clave123', 'contraseña_confirmation' => 'otra1234',
        ])->assertSessionHasErrors(['email', 'contraseña']);

        // y entra al Área clientes con el mail
        $this->postJson(route('cliente.login'), ['usuario' => 'ana@example.com', 'contraseña' => 'clave123'])->assertOk();
    }

    public function test_pantallas_de_clientes_del_admin_cargan(): void
    {
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('secreto'), 'active' => true]);
        $cliente = Cliente::first();

        $this->actingAs($admin)->get(route('adm.clientes'))->assertOk()->assertSee('Mariano Rodríguez');
        $this->actingAs($admin)->get(route('adm.clientes-creador'))->assertOk();
        $this->actingAs($admin)->get(route('adm.clientes-editor', $cliente->id))->assertOk();
        $this->actingAs($admin)->get(route('adm.home'))->assertOk();
        // El producto de la tienda ya no tiene material del Área clientes
        $this->actingAs($admin)->get(route('adm.producto-editor', Producto::first()->id))->assertOk()->assertDontSee('name="documentos', false);
    }

    public function test_admin_crea_producto_del_area_clientes_con_videos_y_documentos(): void
    {
        Storage::fake('public');
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('secreto'), 'active' => true]);

        foreach (['adm.producto-cliente', 'adm.producto-cliente-creador'] as $ruta) {
            $this->actingAs($admin)->get(route($ruta))->assertOk();
        }

        $this->actingAs($admin)->post(route('adm.producto-cliente-store'), [
            'nombre' => 'Hidrolavadora HX-200',
            'videos' => [
                ['titulo' => 'Puesta en marcha', 'archivo' => UploadedFile::fake()->create('puesta.mp4', 2000, 'video/mp4')],
                ['titulo' => 'Mantenimiento', 'archivo' => UploadedFile::fake()->create('mantenimiento.mp4', 1500, 'video/mp4')],
            ],
            'documentos' => [
                ['titulo' => 'Manual de uso', 'archivo' => UploadedFile::fake()->create('manual.pdf', 300, 'application/pdf')],
                ['titulo' => 'Despiece', 'archivo' => UploadedFile::fake()->create('despiece.pdf', 100, 'application/pdf')],
            ],
        ])->assertRedirect(route('adm.producto-cliente'));

        $equipo = ProductoCliente::firstWhere('nombre', 'Hidrolavadora HX-200');
        $this->assertCount(2, $equipo->videosCargados());
        $this->assertCount(2, $equipo->documentosCargados());
        $this->actingAs($admin)->get(route('adm.producto-cliente-editor', $equipo->id))->assertOk()->assertSee('Manual de uso');

        // Cualquier cliente lo ve con sus videos y documentos
        $cliente = Cliente::firstWhere('usuario', 'marianor');
        $this->actingAs($cliente, 'cliente')->get(route('cliente.productos', $equipo->id))->assertOk()
            ->assertSee('Puesta en marcha')
            ->assertSee('data-video="' . $equipo->videosCargados()[0]['archivo'] . '"', false)
            ->assertSee('Despiece');

        // Un archivo que no es video se rechaza
        $this->actingAs($admin)->post(route('adm.producto-cliente-store'), [
            'nombre' => 'Otro', 'videos' => [['titulo' => 'X', 'archivo' => UploadedFile::fake()->create('nota.pdf', 10, 'application/pdf')]],
        ])->assertSessionHasErrors('videos.0.archivo');

        // Borrarlo lo saca del Área clientes
        $this->actingAs($admin)->get(route('adm.producto-cliente-destroy', $equipo->id))->assertRedirect(route('adm.producto-cliente'));
        $this->actingAs($cliente, 'cliente')->get(route('cliente.productos', $equipo->id))->assertNotFound();
    }

    public function test_cliente_inicia_sesion_con_su_nombre(): void
    {
        $this->postJson(route('cliente.login'), ['usuario' => 'Mariano Rodríguez', 'contraseña' => 'dixter2026'])
            ->assertOk()->assertJson(['success' => true]);
        $this->assertAuthenticatedAs(Cliente::firstWhere('usuario', 'marianor'), 'cliente');

        // Ya logueado, el menú no ofrece volver al sitio público
        $this->get(route('cliente.productos'))->assertOk()->assertDontSee('Ir al sitio');
    }

    public function test_admin_de_compradores_solo_tiene_los_datos_del_registro(): void
    {
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('secreto'), 'active' => true, 'rol' => 'Administrador']);
        $comprador = WebUsuario::create(['usuario' => 'Juan Perez', 'email' => 'juan@example.com', 'contraseña' => Hash::make('x'), 'active' => true]);

        $this->actingAs($admin)->get(route('adm.web-usuarios'))->assertOk()
            ->assertSee('Celular')->assertSee('Empresa / Razón social')->assertDontSee('Descuento');
        $this->actingAs($admin)->get(route('adm.web-usuarios-editor', $comprador->id))->assertOk()
            ->assertSee('name="telefono"', false)->assertDontSee('descuento', false)->assertDontSee('name="cuit"', false);

        $this->actingAs($admin)->post(route('adm.web-usuarios-update'), [
            'usuarios_id' => $comprador->id, 'usuario' => 'Juan Pérez', 'email' => 'juan@example.com',
            'telefono' => '11 5555-5555', 'empresa' => 'Limpiezas SA',
        ])->assertRedirect(route('adm.web-usuarios'));

        $comprador->refresh();
        $this->assertSame(['Juan Pérez', '11 5555-5555', 'Limpiezas SA'], [$comprador->usuario, $comprador->telefono, $comprador->empresa]);
    }

    public function test_comprador_se_registra_publicamente_y_queda_logueado(): void
    {
        $this->postJson(route('web.register'), [
            'usuario' => 'Juan Perez',
            'email' => 'juan@example.com',
            'contraseña' => 'clave12345',
            'contraseña_confirmation' => 'clave12345',
        ])->assertOk()->assertJson(['success' => true]);

        $juan = WebUsuario::firstWhere('email', 'juan@example.com');
        $this->assertTrue((bool) $juan->active);
        $this->assertAuthenticatedAs($juan, 'web_usuario');

        $this->get(route('web.cuenta'))->assertOk()->assertSee('Hola, Juan Perez');
    }

    public function test_los_dos_logins_son_independientes(): void
    {
        $this->postJson(route('cliente.login'), ['usuario' => 'marianor', 'contraseña' => 'dixter2026'])->assertOk();

        // Iniciar y cerrar sesión como comprador no cierra la del Área clientes
        $comprador = WebUsuario::create(['usuario' => 'c', 'email' => 'c@example.com', 'contraseña' => Hash::make('x'), 'active' => true]);
        $this->actingAs($comprador, 'web_usuario')->post(route('web.logout'));

        $this->assertAuthenticated('cliente');
    }
}
