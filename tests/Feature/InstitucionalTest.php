<?php

namespace Tests\Feature;

use App\Mail\FormularioMail;
use App\Models\Distribuidore;
use App\Models\Nosotro;
use App\Models\Usuario;
use Database\Seeders\DixterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstitucionalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DixterSeeder::class);
    }

    public function test_las_paginas_institucionales_cargan(): void
    {
        $this->get(route('aplicaciones'))->assertOk()->assertSee('Equipos pensados para cada entorno de trabajo.')->assertSee('Peluquería canina');
        $this->get(route('nuestra-red'))->assertOk()->assertSee('Distribuidor 1')->assertSee('Referencias:');
        $this->get(route('soporte'))->assertOk()->assertSee('Tu equipo, siempre en marcha.')->assertSee('Asesoramiento técnico');
        $this->get(route('post-venta'))->assertOk()->assertSee('Estamos con vos después de la compra.')->assertSee('Arrepentimiento de compra');
        $this->get(route('nosotros'))->assertOk()->assertSee('¿Por qué elegirnos?')->assertSee('Nuestro compromiso')->assertSee('Fabricación nacional');
        $this->get(route('contacto'))->assertOk()->assertSee('Hablemos')->assertSee('mapa-contacto', false);
    }

    public function test_los_links_del_header_ya_no_quedan_en_numeral(): void
    {
        $html = $this->get(route('home'))->getContent();

        foreach (['aplicaciones', 'nuestra-red', 'soporte', 'post-venta', 'nosotros', 'contacto'] as $ruta) {
            $this->assertStringContainsString('href="' . route($ruta) . '"', $html, "Falta el link a {$ruta}");
        }
    }

    public function test_nuestra_red_filtra_por_linea_y_localidad(): void
    {
        $this->get(route('nuestra-red', ['linea' => 'pulidoras']))
            ->assertOk()
            ->assertSee('Distribuidor 4')       // solo pulidoras
            ->assertSee('Distribuidor 1')       // todas las líneas
            ->assertDontSee('Distribuidor 3');  // turbinas

        $this->get(route('nuestra-red', ['localidad' => 'morón']))
            ->assertSee('Distribuidor 3')->assertDontSee('Distribuidor 5');
    }

    public function test_titulo_de_nuestra_red_editable(): void
    {
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);

        $this->actingAs($admin)->get(route('adm.distribuidore'))->assertOk()->assertSee('Título de la página');
        $this->actingAs($admin)->post(route('adm.distribuidore-encabezado'), ['texto' => 'Dónde comprar Dixter'])
            ->assertRedirect(route('adm.distribuidore'));

        $this->get(route('nuestra-red'))->assertOk()->assertSee('Dónde comprar Dixter');
    }

    public function test_el_home_muestra_solo_industrias_destacadas(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('Hotelería')->assertDontSee('Peluquería canina');
        $this->get(route('aplicaciones'))->assertOk()->assertSee('Hotelería')->assertSee('Peluquería canina');

        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);
        $canina = \App\Models\Sectore::firstWhere('nombre', 'Peluquería canina');
        $this->actingAs($admin)->get(route('adm.sectore-destacar', $canina->id))->assertRedirect(route('adm.sectore'));

        $this->get(route('home'))->assertSee('Peluquería canina');
    }

    public function test_textos_de_post_venta_editables(): void
    {
        $this->get(route('post-venta'))->assertSee('Estamos con vos después de la compra.');

        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);
        $this->actingAs($admin)->get(route('adm.postventa'))->assertOk();
        $this->actingAs($admin)->post(route('adm.postventa-update'), ['etiqueta' => 'Servicio', 'titulo' => 'Siempre cerca tuyo.'])
            ->assertRedirect();

        $this->get(route('post-venta'))->assertSee('Servicio')->assertSee('Siempre cerca tuyo.');
    }

    public function test_politica_de_privacidad_es_una_pagina_con_texto_largo_del_admin(): void
    {
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);
        $this->actingAs($admin)->get(route('adm.info'))->assertOk()->assertSee('Política de privacidad');

        // ~300 KB de texto: más de lo que entra en un campo TEXT común (64 KB)
        $largo = '<h2>Datos personales</h2>' . str_repeat('<p>Cláusula de privacidad con bastante texto para probar el largo.</p>', 4500);
        $this->actingAs($admin)->post(route('adm.info-update'), ['privacidad' => $largo])->assertRedirect(route('adm.info'));
        $this->assertSame(strlen($largo), strlen(\App\Models\Info::first()->privacidad));

        $this->get(route('privacidad'))->assertOk()->assertSee('<h2>Datos personales</h2>', false);
    }

    public function test_marcador_segun_lineas(): void
    {
        $this->assertSame('todas', Distribuidore::firstWhere('nombre', 'Distribuidor 1')->claveLinea());
        $this->assertSame('turbinas', Distribuidore::firstWhere('nombre', 'Distribuidor 3')->claveLinea());
        $this->assertSame('#E58C92', Distribuidore::firstWhere('nombre', 'Distribuidor 3')->colorLinea());
    }

    public function test_registro_de_producto_manda_mail(): void
    {
        Mail::fake();

        $this->post(route('post-venta.registro.enviar'), [
            'modelo' => 'Serie 300', 'numero_serie' => 'AB123', 'email' => 'juan@example.com', 'nombre' => 'Juan',
            'apellido' => 'Perez', 'empresa' => 'Limpiezas SA', 'rubro' => 'Alimenticia',
        ])->assertRedirect(route('post-venta.registro'))->assertSessionHas('ok_formulario');

        Mail::assertSent(FormularioMail::class, fn ($m) => $m->hasTo('ventas@dixter.com.ar')
            && $m->asunto === 'Registro de producto'
            && $m->campos['Número de serie'] === 'AB123'
            && $m->hasReplyTo('juan@example.com'));
    }

    public function test_sugerencias_valida_opciones(): void
    {
        Mail::fake();

        $this->post(route('post-venta.sugerencias.enviar'), ['nombre' => 'Ana', 'apellido' => 'Gómez', 'donde' => 'Cualquiera'])
            ->assertSessionHasErrors(['donde', 'calificacion', 'motivo', 'medio']);
        Mail::assertNothingSent();

        $this->post(route('post-venta.sugerencias.enviar'), [
            'nombre' => 'Ana', 'apellido' => 'Gómez', 'donde' => 'Distribuidor', 'calificacion' => 'Excelente',
            'motivo' => 'Precio', 'medio' => 'Redes sociales', 'recomendaria' => 'Sí', 'comentario' => 'Muy buena atención',
        ])->assertSessionHas('ok_formulario');
        Mail::assertSent(FormularioMail::class, fn ($m) => $m->asunto === 'Buzón de sugerencias');
    }

    public function test_arrepentimiento_adjunta_el_archivo(): void
    {
        Mail::fake();

        $this->post(route('post-venta.arrepentimiento.enviar'), [
            'nombre' => 'Juan', 'apellido' => 'Perez', 'email' => 'juan@example.com', 'numero_pedido' => '00000013',
            'fecha_compra' => now()->subDays(3)->format('Y-m-d'), 'motivo' => 'Llegó dañado',
            'adjunto' => UploadedFile::fake()->create('factura.pdf', 200, 'application/pdf'),
        ])->assertSessionHas('ok_formulario');

        Mail::assertSent(FormularioMail::class, function ($m) {
            return $m->asunto === 'Arrepentimiento de compra' && count($m->attachments()) === 1;
        });

        $this->post(route('post-venta.arrepentimiento.enviar'), [
            'nombre' => 'Juan', 'apellido' => 'Perez', 'email' => 'juan@example.com', 'numero_pedido' => '1',
            'fecha_compra' => now()->addDay()->format('Y-m-d'),
        ])->assertSessionHasErrors('fecha_compra');
    }

    public function test_por_que_elegirnos_usa_los_logos_de_nosotros(): void
    {
        Storage::fake('public');
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);

        $this->actingAs($admin)->post(route('adm.nosotros-update'), [
            'nosotro_id' => 1, 'texto1' => 'Desde 1947', 'texto2' => 'Eleginos',
            'nombre_icono4' => 'Cuarto logo', 'texto_icono4' => '<p>Texto del cuarto</p>',
            'icono4' => UploadedFile::fake()->image('logo4.png', 60, 60),
        ])->assertRedirect();

        $nosotros = Nosotro::first();
        $this->assertSame('Cuarto logo', $nosotros->nombre_icono4);
        $this->assertNotNull($nosotros->icono4);

        $this->get(route('nosotros'))->assertOk()
            ->assertSee('Eleginos')
            ->assertSee('Cuarto logo')
            ->assertSee('Texto del cuarto', false);
    }

    public function test_formulario_de_contacto(): void
    {
        Mail::fake();

        $this->postJson(route('form.contacto'), ['nombre' => 'Juan Perez', 'email' => 'juan@example.com', 'mensaje' => 'Hola'])
            ->assertOk()->assertJson(['success' => true]);
        Mail::assertSent(FormularioMail::class, fn ($m) => $m->hasTo('ventas@dixter.com.ar') && $m->campos['Mensaje'] === 'Hola');

        $this->postJson(route('form.contacto'), ['nombre' => 'Juan'])->assertStatus(422);
    }

    public function test_admin_de_los_modulos_nuevos(): void
    {
        $admin = Usuario::create(['usuario' => 'admin', 'contraseña' => Hash::make('x'), 'active' => true]);

        foreach (['adm.distribuidore', 'adm.distribuidore-creador', 'adm.compromiso', 'adm.soporte', 'adm.nosotros'] as $ruta) {
            $this->actingAs($admin)->get(route($ruta))->assertOk();
        }

        $this->actingAs($admin)->post(route('adm.distribuidore-store'), [
            'nombre' => 'Distribuidor Norte', 'provincia' => 'Córdoba', 'localidad' => 'Córdoba',
            'latitud' => '-31.4201', 'longitud' => '-64.1888', 'lineas' => ['turbinas', 'pulidoras'],
        ])->assertRedirect(route('adm.distribuidore'));

        $nuevo = Distribuidore::firstWhere('nombre', 'Distribuidor Norte');
        $this->assertSame(['turbinas', 'pulidoras'], $nuevo->lineas);
        $this->assertSame(-31.4201, $nuevo->latitud);

        $this->actingAs($admin)->post(route('adm.distribuidore-store'), ['nombre' => 'X', 'lineas' => ['inventada']])
            ->assertSessionHasErrors('lineas.0');
    }
}
