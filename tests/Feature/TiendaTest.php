<?php

namespace Tests\Feature;

use App\Mail\PedidoEstadoMail;
use App\Models\Categoria;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\WebUsuario;
use App\Services\Carrito;
use App\Services\MercadoPagoService;
use Database\Seeders\DixterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use MercadoPago\Resources\Payment;
use MercadoPago\Resources\Preference;
use Mockery;
use Tests\TestCase;

class TiendaTest extends TestCase
{
    use RefreshDatabase;

    private WebUsuario $comprador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DixterSeeder::class);
        $this->comprador = WebUsuario::create([
            'usuario' => 'Juan Perez', 'email' => 'juan@example.com', 'contraseña' => Hash::make('clave12345'), 'active' => true,
        ]);
    }

    private function producto(string $codigo): Producto
    {
        return Producto::where('codigo', $codigo)->firstOrFail();
    }

    private function datosCompra(array $extra = []): array
    {
        return $extra + [
            'nombre' => 'Juan Perez', 'dni' => '30111222', 'cuit' => '20301112223', 'email' => 'juan@example.com',
            'celular' => '1155556666', 'direccion' => 'Calle 123', 'localidad' => 'CABA', 'provincia' => 'Buenos Aires',
            'codigo_postal' => '1000', 'envio' => 'retiro_local', 'forma_pago' => 'pago_local', 'terminos' => '1',
        ];
    }

    // ---------------------------------------------------------------- catálogo

    public function test_listado_filtra_por_categoria_con_subcategorias(): void
    {
        $aspiradoras = Categoria::where('nombre', 'Aspiradoras')->first();
        $lavadoras = Categoria::where('nombre', 'Lavadora de pisos')->first();

        $this->get(route('productos', ['categoria' => $aspiradoras->id]))
            ->assertOk()
            ->assertSee('Super Vac 341-16')       // está en "Profesional Serie 300", hija de Aspiradoras
            ->assertDontSee('Super Kliner');

        $this->get(route('productos', ['categoria' => $lavadoras->id]))
            ->assertOk()->assertSee('Super Kliner')->assertDontSee('Super Vac 341-16');
    }

    public function test_listado_filtra_por_precio_y_muestra_ofertas_y_consultar(): void
    {
        $this->get(route('productos'))
            ->assertOk()->assertSee('OFERTA')->assertSee('Consultar')->assertSee('$498.999,00');

        $this->get(route('productos', ['min' => 400000, 'max' => 600000]))
            ->assertOk()->assertSee('Super Vac 341-24')->assertDontSee('Super Vac 321-16')->assertDontSee('321-60C');
    }

    public function test_detalle_muestra_sin_iva_solo_si_tiene_iva(): void
    {
        $producto = $this->producto('2410');

        $this->get(route('productos.detalle', $producto))
            ->assertOk()
            ->assertSee('$410.000,00')
            ->assertSee('$338.842,98 sin IVA', false)   // 410000 / 1,21
            ->assertSee('Modelos')
            ->assertSee('2360 mm H20')
            ->assertSee('Agregar al carrito');

        $producto->update(['iva' => 0]);
        $this->get(route('productos.detalle', $producto))->assertOk()->assertDontSee('sin IVA');

        $producto->update(['iva' => 10.5]);
        $this->get(route('productos.detalle', $producto))->assertSee('$371.040,72 sin IVA', false); // 410000 / 1,105
    }

    public function test_producto_sin_precio_no_se_puede_comprar(): void
    {
        $consultar = $this->producto('2231');

        $this->get(route('productos.detalle', $consultar))->assertOk()->assertSee('Consultar por este producto')->assertDontSee('id="cantidad-producto"', false);

        $this->actingAs($this->comprador, 'web_usuario')
            ->postJson(route('web-carrito.agregar'), ['producto_id' => $consultar->id])
            ->assertStatus(422);
    }

    // ---------------------------------------------------------------- carrito

    public function test_sin_sesion_el_carrito_lleva_al_login(): void
    {
        $this->get(route('web-carrito'))->assertRedirect(route('web.cuenta'));

        $this->postJson(route('web-carrito.agregar'), ['producto_id' => $this->producto('2410')->id])
            ->assertStatus(401)
            ->assertJson(['redirect' => route('web.cuenta')]);

        $this->postJson(route('web-favorito', $this->producto('2410')))->assertStatus(401);
    }

    public function test_agregar_actualizar_y_quitar_del_carrito(): void
    {
        $p1 = $this->producto('2410');
        $p2 = $this->producto('2210');
        $this->actingAs($this->comprador, 'web_usuario');

        $this->postJson(route('web-carrito.agregar'), ['producto_id' => $p1->id, 'cantidad' => 2])
            ->assertOk()->assertJson(['success' => true, 'cantidad' => 1, 'totales' => ['total' => 820000]]);
        $this->postJson(route('web-carrito.agregar'), ['producto_id' => $p2->id])->assertJson(['cantidad' => 2]);

        $this->postJson(route('web-carrito.actualizar'), ['producto_id' => $p1->id, 'cantidad' => 1])
            ->assertJson(['totales' => ['total' => 758500]]);   // 410000 + 348500

        $this->postJson(route('web-carrito.quitar'), ['producto_id' => $p2->id])->assertJson(['cantidad' => 1]);

        $this->get(route('web-carrito'))->assertOk()->assertSee('Total del carrito')->assertSee('Super Vac 341-16')->assertSee('Finalizar compra');
    }

    public function test_el_precio_lo_pone_el_servidor(): void
    {
        $producto = $this->producto('2410');
        $this->actingAs($this->comprador, 'web_usuario')
            ->postJson(route('web-carrito.agregar'), ['producto_id' => $producto->id, 'precio' => 1])
            ->assertJson(['totales' => ['total' => 410000]]);
    }

    public function test_favoritos(): void
    {
        $producto = $this->producto('2410');
        $this->actingAs($this->comprador, 'web_usuario');

        $this->postJson(route('web-favorito', $producto))->assertJson(['favorito' => true]);
        $this->get(route('web.cuenta'))->assertSee('Mis favoritos')->assertSee('Super Vac 341-16');
        $this->postJson(route('web-favorito', $producto))->assertJson(['favorito' => false]);
    }

    // ---------------------------------------------------------------- compra

    public function test_compra_con_pago_en_local(): void
    {
        Mail::fake();
        $this->actingAs($this->comprador, 'web_usuario');
        (new Carrito($this->comprador))->agregar($this->producto('2410'), 2);

        $this->get(route('compra'))->assertOk()->assertSee('Detalle de facturación')->assertSee('Realizar compra')
            ->assertSee('href="' . route('privacidad') . '"', false); // la Política de privacidad es una página propia

        $respuesta = $this->post(route('compra.realizar'), $this->datosCompra(['mensaje' => 'Retiro el lunes']));

        $pedido = Pedido::latest('id')->first();
        $respuesta->assertRedirect(route('compra.confirmado', $pedido));
        $this->assertSame(820000.0, $pedido->total);
        $this->assertSame(677685.95, $pedido->subtotal);
        $this->assertSame('pago_local', $pedido->forma_pago);
        $this->assertNull($pedido->pago_estado);
        $this->assertSame('en_espera', $pedido->estado);   // todo pedido nace "En espera"
        $this->assertCount(1, $pedido->items);
        $this->assertSame([], (new Carrito($this->comprador))->items());
        // "En espera": al cliente y a la casilla de ventas (con Responder a = cliente)
        Mail::assertSent(PedidoEstadoMail::class, 2);
        Mail::assertSent(PedidoEstadoMail::class, fn ($m) => $m->hasTo('juan@example.com') && !$m->paraDixter
            && $m->estado === 'en_espera' && $m->hasSubject('¡Hemos recibido tu pedido en Dixter!'));
        Mail::assertSent(PedidoEstadoMail::class, fn ($m) => $m->hasTo('ventas@dixter.com.ar') && $m->paraDixter
            && $m->hasReplyTo('juan@example.com') && $m->hasSubject('[Dixter]: Tenés un nuevo pedido: ' . $pedido->numero()));

        $this->get(route('compra.confirmado', $pedido))->assertOk()->assertSee('Pedido confirmado')->assertSee($pedido->numero());
    }

    public function test_compra_valida_datos_y_terminos(): void
    {
        $this->actingAs($this->comprador, 'web_usuario');
        (new Carrito($this->comprador))->agregar($this->producto('2410'), 1);

        $this->post(route('compra.realizar'), $this->datosCompra(['terminos' => null, 'dni' => '']))
            ->assertSessionHasErrors(['terminos', 'dni']);
        $this->assertSame(0, Pedido::count());
    }

    public function test_no_se_puede_ver_el_pedido_de_otro(): void
    {
        $this->actingAs($this->comprador, 'web_usuario');
        (new Carrito($this->comprador))->agregar($this->producto('2410'), 1);
        Mail::fake();
        $this->post(route('compra.realizar'), $this->datosCompra());
        $pedido = Pedido::latest('id')->first();

        $otro = WebUsuario::create(['usuario' => 'otro', 'email' => 'otro@example.com', 'contraseña' => Hash::make('x'), 'active' => true]);
        $this->actingAs($otro, 'web_usuario')->get(route('compra.confirmado', $pedido))->assertNotFound();
    }

    public function test_compra_con_mercado_pago_y_webhook(): void
    {
        Mail::fake();
        $preferencia = new Preference();
        $preferencia->init_point = 'https://www.mercadopago.com.ar/checkout/v1/redirect?pref_id=123';

        $mp = Mockery::mock(MercadoPagoService::class)->makePartial();
        $mp->shouldReceive('configurado')->andReturn(true);
        $mp->shouldReceive('crearPreferencia')->once()->andReturn($preferencia);
        $mp->shouldReceive('pagoDelPedido')->andReturn(null);
        $this->app->instance(MercadoPagoService::class, $mp);

        $this->actingAs($this->comprador, 'web_usuario');
        (new Carrito($this->comprador))->agregar($this->producto('2410'), 1);

        $this->post(route('compra.realizar'), $this->datosCompra(['forma_pago' => 'mercado_pago']))
            ->assertRedirect(route('compra.mp.pagar', Pedido::latest('id')->first()));
        $pedido = Pedido::latest('id')->first();
        $this->assertSame('pendiente', $pedido->pago_estado);
        $this->assertSame('en_espera', $pedido->estado);
        Mail::assertSent(PedidoEstadoMail::class, 2);   // los de "En espera" salen al crear el pedido

        $this->get(route('compra.mp.pagar', $pedido))->assertRedirect('https://www.mercadopago.com.ar/checkout/v1/redirect?pref_id=123');

        // Notificación de Mercado Pago: pago aprobado por el total exacto
        $pago = new Payment();
        $pago->id = 999;
        $pago->status = 'approved';
        $pago->status_detail = 'accredited';
        $pago->transaction_amount = 410000;
        $pago->external_reference = (string) $pedido->id;
        $mp->shouldReceive('obtenerPago')->with(999)->andReturn($pago);

        $this->postJson(route('mercadopago.webhook') . '?data_id=999', ['type' => 'payment', 'data' => ['id' => '999']])->assertNoContent();

        $pedido->refresh();
        $this->assertSame('aprobado', $pedido->pago_estado);
        $this->assertSame('en_espera', $pedido->estado);   // "Procesando" lo pone Dixter a mano
        $this->assertSame('999', $pedido->mp_payment_id);

        // Una segunda notificación del mismo pago no manda nada
        $this->postJson(route('mercadopago.webhook') . '?data_id=999', ['type' => 'payment'])->assertNoContent();
        Mail::assertSent(PedidoEstadoMail::class, 2);

        $this->get(route('compra.confirmado', $pedido))->assertSee('Pedido confirmado');
    }

    public function test_webhook_rechaza_montos_que_no_coinciden(): void
    {
        $pedido = Pedido::create(['usuario_id' => $this->comprador->id, 'forma_pago' => 'mercado_pago', 'pago_estado' => 'pendiente', 'total' => 410000]);
        $pago = new Payment();
        $pago->id = 5;
        $pago->status = 'approved';
        $pago->transaction_amount = 10;
        $pago->external_reference = (string) $pedido->id;

        $mp = Mockery::mock(MercadoPagoService::class)->makePartial();
        $mp->shouldReceive('obtenerPago')->andReturn($pago);
        $this->app->instance(MercadoPagoService::class, $mp);

        $this->postJson(route('mercadopago.webhook') . '?data_id=5', ['type' => 'payment'])->assertNoContent();
        $this->assertSame('pendiente', $pedido->fresh()->pago_estado);
        $this->assertSame('en_espera', $pedido->fresh()->estado);
    }

    public function test_pago_rechazado_deja_el_pedido_en_fallo_y_no_pisa_lo_que_cambio_el_admin(): void
    {
        Mail::fake();
        $pedido = Pedido::create([
            'usuario_id' => $this->comprador->id, 'forma_pago' => 'mercado_pago', 'pago_estado' => 'pendiente', 'total' => 410000,
            'nombre' => 'Juan Perez', 'email' => 'juan@example.com',
        ]);
        $mp = app(MercadoPagoService::class);
        $pago = function (int $id, string $status) use ($pedido) {
            $p = new Payment();
            $p->id = $id;
            $p->status = $status;
            $p->status_detail = $status;
            $p->transaction_amount = 410000;
            $p->external_reference = (string) $pedido->id;
            return $p;
        };

        $mp->aplicarPago($pedido, $pago(1, 'rejected'));
        $this->assertSame('fallido', $pedido->fresh()->estado);
        Mail::assertSent(PedidoEstadoMail::class, fn ($m) => $m->estado === 'fallido' && $m->hasTo('juan@example.com')
            && $m->hasSubject('Tu pedido en Dixter no se realizó correctamente'));
        Mail::assertSent(PedidoEstadoMail::class, fn ($m) => $m->estado === 'fallido' && $m->hasTo('ventas@dixter.com.ar')
            && $m->hasSubject('Dixter: El pedido ' . ltrim($pedido->numero(), '#') . ' ha fallado'));

        // Un reintento aprobado lo devuelve a "En espera", sin volver a mandar "Recibimos tu pedido"
        $mp->aplicarPago($pedido->fresh(), $pago(2, 'approved'));
        $this->assertSame('en_espera', $pedido->fresh()->estado);
        Mail::assertSent(PedidoEstadoMail::class, 2);   // solo los 2 de Falló

        // Si el admin ya lo despachó, el pago no lo mueve
        $pedido->update(['estado' => 'despachado']);
        $mp->aplicarPago($pedido->fresh(), $pago(2, 'approved'));
        $this->assertSame('despachado', $pedido->fresh()->estado);
    }

    public function test_modo_prueba_de_mercado_pago_no_manda_el_mail_del_cliente(): void
    {
        $mp = app(MercadoPagoService::class);

        config(['services.mercadopago.modo_prueba' => false]);
        $this->assertSame(['email' => 'juan@example.com'], $mp->pagador('juan@example.com'));

        config(['services.mercadopago.modo_prueba' => true, 'services.mercadopago.pagador_prueba' => null]);
        $this->assertSame([], $mp->pagador('juan@example.com'));

        config(['services.mercadopago.pagador_prueba' => 'test_user_1@testuser.com']);
        $this->assertSame(['email' => 'test_user_1@testuser.com'], $mp->pagador('juan@example.com'));
    }

    // ---------------------------------------------------------------- mails de estados

    private function pedidoRealizado(): Pedido
    {
        $this->actingAs($this->comprador, 'web_usuario');
        (new Carrito($this->comprador))->agregar($this->producto('2410'), 1);
        $this->post(route('compra.realizar'), $this->datosCompra());

        return Pedido::latest('id')->first();
    }

    public function test_cada_cambio_de_estado_del_admin_manda_su_mail(): void
    {
        Mail::fake();
        $pedido = $this->pedidoRealizado();
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);

        // Casilla de ventas con copia, desde Tienda > Info. de compra
        $this->actingAs($admin)->post(route('adm.info-update'), ['mail_ventas' => 'pedidos@dixter.com.ar', 'mail_ventas_cc' => 'gerencia@dixter.com.ar'])
            ->assertRedirect(route('adm.info'));

        $esperados = [
            'procesando' => ['¡Hemos recibido tu pedido en Dixter!', false],
            'despachado' => ['Tu pedido en Dixter fue despachado', false],
            'completado' => ['Tu pedido en Dixter está completo', false],
            'fallido' => ['Tu pedido en Dixter no se realizó correctamente', true],
            'cancelado' => ['Tu pedido en Dixter fue cancelado', true],
        ];
        foreach ($esperados as $estado => [$asunto, $avisaAVentas]) {
            Mail::fake();
            $this->actingAs($admin)->post(route('adm.pedidos-historico-estado', $pedido), ['estado' => $estado])->assertRedirect();

            Mail::assertSent(PedidoEstadoMail::class, fn ($m) => $m->hasTo('juan@example.com') && $m->estado === $estado && $m->hasSubject($asunto));
            if ($avisaAVentas) {
                Mail::assertSent(PedidoEstadoMail::class, fn ($m) => $m->hasTo('pedidos@dixter.com.ar') && $m->hasCc('gerencia@dixter.com.ar') && $m->paraDixter);
            }
            Mail::assertSent(PedidoEstadoMail::class, $avisaAVentas ? 2 : 1);
        }

        // Guardar el editor sin cambiar el estado no manda nada; volver a "En espera" tampoco
        Mail::fake();
        $this->actingAs($admin)->post(route('adm.pedidos-historico-update'), ['pedido_id' => $pedido->id, 'estado' => 'cancelado', 'fecha_entrega' => '2026-12-01']);
        $this->actingAs($admin)->post(route('adm.pedidos-historico-estado', $pedido), ['estado' => 'en_espera']);
        Mail::assertNothingSent();
    }

    public function test_los_mails_replican_el_documento_de_pedidos(): void
    {
        Mail::fake();
        $pedido = $this->pedidoRealizado()->load('items');

        $interno = (new PedidoEstadoMail($pedido, 'en_espera', true))->render();
        $this->assertStringContainsString('Nuevo pedido: n.º ' . ltrim($pedido->numero(), '#'), $interno);
        $this->assertStringContainsString('Recibiste un nuevo pedido de Juan Perez:', $interno);
        $this->assertStringContainsString('Resumen del pedido', $interno);
        $this->assertStringContainsString('Super Vac 341-16', $interno);
        $this->assertStringContainsString('Dirección de facturación', $interno);
        $this->assertStringContainsString('Dirección de envío', $interno);
        $this->assertStringContainsString('Felicitaciones por la venta.', $interno);

        $cliente = (new PedidoEstadoMail($pedido, 'en_espera'))->render();
        $this->assertStringContainsString('Gracias por tu pedido', $cliente);
        $this->assertStringContainsString('Hola Juan Perez,', $cliente);
        $this->assertStringContainsString('está actualmente en estado de espera', $cliente);
        $this->assertStringContainsString('(incluye $', $cliente);
        $this->assertStringContainsString('Método de pago:', $cliente);
        $this->assertStringContainsString('ventas@dixter.com.ar', $cliente);

        $completado = (new PedidoEstadoMail($pedido, 'completado'))->render();
        $this->assertStringContainsString(route('post-venta.registro'), $completado);

        $fallido = (new PedidoEstadoMail($pedido, 'fallido', true))->render();
        $this->assertStringContainsString('Pedido fallido: ' . $pedido->numero(), $fallido);
        $this->assertStringContainsString('ha dado error al realizar el pago con Pago en local', $fallido);
    }

    public function test_el_cliente_cancela_su_pedido_solo_en_espera(): void
    {
        Mail::fake();
        $pedido = $this->pedidoRealizado();

        $this->get(route('web.cuenta'))->assertSee('data-cancelar-pedido=', false);

        Mail::fake();
        $this->post(route('pedido.cancelar', $pedido))->assertRedirect(route('web.cuenta'))->assertSessionHas('ok_cuenta');
        $this->assertSame('cancelado', $pedido->fresh()->estado);
        Mail::assertSent(PedidoEstadoMail::class, 2);
        Mail::assertSent(PedidoEstadoMail::class, fn ($m) => $m->paraDixter && $m->porCliente
            && str_contains($m->render(), 'fue cancelado por el cliente desde Mi cuenta'));
        $this->get(route('web.cuenta'))->assertDontSee('data-cancelar-pedido=', false);

        // Si Dixter ya lo avanzó, tiene que comunicarse
        $otro = $this->pedidoRealizado();
        $otro->update(['estado' => 'procesando']);
        Mail::fake();
        $this->post(route('pedido.cancelar', $otro))->assertSessionHas('error_cuenta');
        $this->assertSame('procesando', $otro->fresh()->estado);
        Mail::assertNothingSent();

        // No puede cancelar el pedido de otro
        $ajeno = WebUsuario::create(['usuario' => 'otro', 'email' => 'otro@example.com', 'contraseña' => Hash::make('x'), 'active' => true]);
        $this->actingAs($ajeno, 'web_usuario')->post(route('pedido.cancelar', $otro))->assertNotFound();
    }

    // ---------------------------------------------------------------- admin

    public function test_admin_categorias_hasta_tres_niveles(): void
    {
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);
        $serie300 = Categoria::where('nombre', 'Profesional Serie 300')->first();   // nivel 3

        $this->actingAs($admin)->get(route('adm.categorias'))->assertOk()->assertSee('Profesional Serie 300');

        $this->actingAs($admin)->post(route('adm.categorias-store'), ['nombre' => 'Nivel 4', 'parent_id' => $serie300->id])
            ->assertSessionHasErrors('parent_id');
        $this->assertDatabaseMissing('categorias', ['nombre' => 'Nivel 4']);
    }

    public function test_admin_pedidos_e_info_de_compra(): void
    {
        Mail::fake();
        $this->actingAs($this->comprador, 'web_usuario');
        (new Carrito($this->comprador))->agregar($this->producto('2410'), 1);
        $this->post(route('compra.realizar'), $this->datosCompra(['razon_social' => 'Limpiezas SA']));
        $pedido = Pedido::latest('id')->first();

        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);
        $this->actingAs($admin)->get(route('adm.pedidos-historico'))->assertOk()->assertSee('Limpiezas SA')->assertSee('Retiro en local');
        $this->actingAs($admin)->get(route('adm.pedidos-historico-editor', $pedido))->assertOk()->assertSee('Pago en local')->assertSee('30111222');
        $this->actingAs($admin)->post(route('adm.pedidos-historico-update'), ['pedido_id' => $pedido->id, 'estado' => 'despachado'])->assertRedirect();
        $this->assertSame('despachado', $pedido->fresh()->estado);

        // Cambio rápido desde el listado, solo con los 6 estados
        $this->actingAs($admin)->get(route('adm.pedidos-historico'))->assertOk()
            ->assertSee('En espera')->assertSee('Procesando')->assertSee('Falló')->assertSee('Completado');
        $this->actingAs($admin)->post(route('adm.pedidos-historico-estado', $pedido), ['estado' => 'completado'])->assertRedirect();
        $this->assertSame('completado', $pedido->fresh()->estado);
        $this->actingAs($admin)->post(route('adm.pedidos-historico-estado', $pedido), ['estado' => 'entregado'])->assertSessionHasErrors('estado');

        $this->actingAs($admin)->get(route('adm.info'))->assertOk()->assertSee('Retiro en local');
        $this->actingAs($admin)->post(route('adm.info-update'), ['retiro_texto' => 'Nuevo texto de retiro'])->assertRedirect(route('adm.info'));
        $this->actingAs($this->comprador, 'web_usuario');
        (new Carrito($this->comprador))->agregar($this->producto('2410'), 1);
        $this->get(route('web-carrito'))->assertSee('Nuevo texto de retiro');
    }

    public function test_admin_guarda_iva_y_pestanas_del_producto(): void
    {
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);
        $producto = $this->producto('2420');

        $this->actingAs($admin)->get(route('adm.producto-editor', $producto->id))->assertOk()->assertSee('IVA')->assertSee('Precio anterior');

        $this->actingAs($admin)->post(route('adm.producto-update'), [
            'producto_id' => $producto->id,
            'nombre' => $producto->nombre,
            'codigo' => $producto->codigo,
            'precio' => 500000,
            'precio_anterior' => 600000,
            'iva' => '10.5',
            'categoria_id' => $producto->categoria_id,
            'ficha' => [['nombre' => 'Modelo', 'valor' => '341-24'], ['nombre' => '', 'valor' => '']],
            'informacion' => [['nombre' => 'Peso', 'valor' => '10 kg']],
        ])->assertRedirect(route('adm.producto'));

        $producto->refresh();
        $this->assertSame(10.5, $producto->iva);
        $this->assertTrue($producto->enOferta());
        $this->assertCount(1, $producto->ficha);   // la fila vacía se descarta

        $this->actingAs($admin)->post(route('adm.producto-update'), ['producto_id' => $producto->id, 'iva' => '15'])
            ->assertSessionHasErrors('iva');
    }

    public function test_videos_del_producto_son_archivos_subidos(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);
        $producto = $this->producto('2420');
        $video = \Illuminate\Http\UploadedFile::fake()->create('uso.mp4', 3000, 'video/mp4');

        $this->actingAs($admin)->post(route('adm.producto-update'), [
            'producto_id' => $producto->id, 'nombre' => $producto->nombre, 'iva' => '21',
            'videos_web' => [['titulo' => 'Cómo usarla', 'archivo' => $video], ['titulo' => 'Sin archivo']],
        ])->assertRedirect(route('adm.producto'));

        $producto->refresh();
        $this->assertCount(1, $producto->videos_web);   // la fila sin archivo se descarta
        $archivo = $producto->videos_web[0]['archivo'];

        $this->get(route('productos.detalle', $producto->id))->assertOk()
            ->assertSee('<video src="' . $archivo . '#t=0.5"', false)
            ->assertSee('Cómo usarla');

        // Un archivo que no es video se rechaza
        $this->actingAs($admin)->post(route('adm.producto-update'), [
            'producto_id' => $producto->id, 'iva' => '21',
            'videos_web' => [['titulo' => 'X', 'archivo' => \Illuminate\Http\UploadedFile::fake()->create('a.pdf', 5, 'application/pdf')]],
        ])->assertSessionHasErrors('videos_web.0.archivo');
    }
}
