<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Middleware\AdminAuth;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\PerfilController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeBannerController;
use App\Http\Controllers\NosotroController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\NovedadeController;
use App\Http\Controllers\ContactoController;

use App\Http\Controllers\WebUsuarioController;
use App\Http\Controllers\ListaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\BannerController;

use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\LogoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AreaClientesController;
use App\Http\Controllers\MetadatoController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\InstitucionalController;

//ADMIN
Route::middleware(AdminAuth::class)->group(function () {

    //ADMIN
    Route::get('/admin/dashboard', function () {
    return view('content.admin.admin-dashboard');})->name('adm.dashboard');

    //perfil
    Route::get('/admin/perfil', [PerfilController::class, 'show'])->name('adm.perfil');
    Route::post('/admin/perfil/update', [PerfilController::class, 'update'])->name('adm.perfil-update');

    //home banner
    Route::get('/admin/home/banner', [HomeBannerController::class,'index'])->name('adm.home-ban');
    Route::get('/admin/home/banner/creador', [HomeBannerController::class,'create'])->name('adm.home-ban-creador');
    Route::post('/admin/home/banner/store', [HomeBannerController::class,'store'])->name('adm.home-ban-store');
    Route::get('/admin/home/banner/editor/{home_banners_id}', [HomeBannerController::class,'show'])->name('adm.home-ban-editor');
    Route::post('/admin/home/banner/update', [HomeBannerController::class,'update'])->name('adm.home-ban-update');
    Route::get('/admin/home/banner/destroy/{home_banners_id}', [HomeBannerController::class,'destroy'])->name('adm.home-ban-destroy');
    Route::get('/admin/home/banner/switch/{home_banners_id}', [HomeBannerController::class,'switch'])->name('adm.home-ban-switch');
    Route::post('/admin/home-ban/reordenar', [HomeBannerController::class, 'reordenar'])->name('adm.home-ban-reordenar');

    //home 
    Route::get('/admin/home', [HomeController::class, 'show'])->name('adm.home');
    Route::post('/admin/home/update', [HomeController::class,'update'])->name('adm.home-update');

    //nosotros
    Route::get('/admin/nosotros', [NosotroController::class, 'show'])->name('adm.nosotros');
    Route::post('/admin/nosotros/update', [NosotroController::class,'update'])->name('adm.nosotros-update');

    // nuestra red: título de la página (los distribuidores están en routes/modulos.php)
    Route::post('/admin/distribuidore/encabezado', [\App\Http\Controllers\DistribuidoreController::class, 'encabezado'])->name('adm.distribuidore-encabezado');

     // marcas

    Route::get('/admin/marca', [MarcaController::class, 'index'])->name('adm.marca');
    Route::get('/admin/marca/creador', [MarcaController::class, 'create'])->name('adm.marca-creador');
    Route::post('/admin/marca/store', [MarcaController::class, 'store'])->name('adm.marca-store');
    Route::get('/admin/marca/editor/{marca_id}', [MarcaController::class, 'show'])->name('adm.marca-editor');
    Route::post('/admin/marca/update', [MarcaController::class, 'update'])->name('adm.marca-update');
    Route::get('/admin/marca/destroy/{marca_id}', [MarcaController::class, 'destroy'])->name('adm.marca-destroy');
    Route::get('/admin/marca/switch/{marca_id}', [MarcaController::class, 'switch'])->name('adm.marca-switch');
    Route::get('/admin/marca/destacado/{marca_id}', [MarcaController::class, 'destacado'])->name('adm.marca-destacado');
    Route::post('/admin/marca/reordenar', [MarcaController::class, 'reordenar'])->name('adm.marca-reordenar');

    // categorías del catálogo (árbol de hasta 3 niveles)
    Route::get('/admin/categorias', [CategoriaController::class, 'index'])->name('adm.categorias');
    Route::post('/admin/categorias/store', [CategoriaController::class, 'store'])->name('adm.categorias-store');
    Route::post('/admin/categorias/update/{id}', [CategoriaController::class, 'update'])->name('adm.categorias-update');
    Route::post('/admin/categorias/destroy/{id}', [CategoriaController::class, 'destroy'])->name('adm.categorias-destroy');
    Route::get('/admin/categorias/switch/{id}', [CategoriaController::class, 'switch'])->name('adm.categorias-switch');

    // productos
    Route::get('/admin/producto', [ProductoController::class, 'index'])->name('adm.producto');
    Route::get('/admin/producto/creador', [ProductoController::class, 'create'])->name('adm.producto-creador');
    Route::post('/admin/producto/store', [ProductoController::class, 'store'])->name('adm.producto-store');
    Route::get('/admin/producto/editor/{producto_id}', [ProductoController::class, 'show'])->name('adm.producto-editor');
    Route::post('/admin/producto/update', [ProductoController::class, 'update'])->name('adm.producto-update');
    Route::get('/admin/producto/destroy/{producto_id}', [ProductoController::class, 'destroy'])->name('adm.producto-destroy');
    Route::get('/admin/producto/switch/{producto_id}', [ProductoController::class, 'switch'])->name('adm.producto-switch');
    Route::get('/admin/producto/destacado/{producto_id}', [ProductoController::class, 'destacado'])->name('adm.producto-destacado');
    Route::get('/admin/producto/delete-archivo/{producto_id}', [ProductoController::class, 'deleteArchivo'])->name('adm.producto-delete-archivo');
    Route::post('/admin/producto/import', [ProductoController::class, 'import'])->name('adm.producto-import');
    Route::post('/admin/producto/fotos-masivas', [ProductoController::class, 'fotosMasivas'])->name('adm.producto-fotos-masivas');
    Route::post('/admin/producto/reordenar', [ProductoController::class, 'reordenar'])->name('adm.producto-reordenar');
    // galería de fotos del producto (el editor ya las usaba pero no estaban registradas)
    Route::post('/admin/producto/fotos/store', [ProductoController::class, 'fotosStore'])->name('adm.producto-fotos-store');
    Route::post('/admin/producto/fotos/reordenar', [ProductoController::class, 'fotosReordenar'])->name('adm.producto-fotos-reordenar');
    Route::get('/admin/producto/fotos/destroy/{foto_id}', [ProductoController::class, 'fotosDestroy'])->name('adm.producto-fotos-destroy');
    Route::get('/admin/producto/fotos/switch/{foto_id}', [ProductoController::class, 'fotosSwitch'])->name('adm.producto-fotos-switch');

    //novedades
    Route::get('/admin/novedade', [NovedadeController::class, 'index'])->name('adm.novedade');
    Route::get('/admin/novedade/creador', [NovedadeController::class, 'create'])->name('adm.novedade-creador');
    Route::post('/admin/novedade/store', [NovedadeController::class, 'store'])->name('adm.novedade-store');
    Route::get('/admin/novedade/editor/{novedade_id}', [NovedadeController::class, 'show'])->name('adm.novedade-editor');
    Route::post('/admin/novedade/update', [NovedadeController::class, 'update'])->name('adm.novedade-update');
    Route::get('/admin/novedade/destroy/{novedade_id}', [NovedadeController::class, 'destroy'])->name('adm.novedade-destroy');
    Route::get('/admin/novedade/switch/{novedade_id}', [NovedadeController::class, 'switch'])->name('adm.novedade-switch');
    Route::get('/admin/novedade/destacado/{novedade_id}', [NovedadeController::class, 'destacado'])->name('adm.novedade-destacado');
    Route::post('/admin/novedade/reordenar', [NovedadeController::class, 'reordenar'])->name('adm.novedade-reordenar');

    // contacto 
    Route::get('/admin/contacto', [ContactoController::class, 'show'])->name('adm.contacto');
    Route::post('/admin/contacto/update', [ContactoController::class, 'update'])->name('adm.contacto.update');

     //web-usuarios
    Route::get('/admin/web-usuarios', [WebUsuarioController::class,'index'])->name('adm.web-usuarios');
    Route::get('/admin/web-usuarios/editor/{usuario_id}', [WebUsuarioController::class,'show'])->name('adm.web-usuarios-editor');
    Route::post('/admin/web-usuarios/import', [WebUsuarioController::class, 'import'])->name('adm.web-usuarios-import');
    Route::get('web-usuarios/export', [WebUsuarioController::class, 'export'])->name('adm.web-usuarios-export');
    Route::post('/admin/web-usuarios/update', [WebUsuarioController::class,'update'])->name('adm.web-usuarios-update');
    Route::get('/admin/web-usuarios/destroy/{usuario_id}', [WebUsuarioController::class,'destroy'])->name('adm.web-usuarios-destroy');
    Route::get('/admin/web-usuarios/switch/{usuario_id}', [WebUsuarioController::class,'switch'])->name('adm.web-usuarios-switch');

    // Lista
    Route::get('/admin/lista', [ListaController::class, 'index'])->name('adm.lista');
    Route::get('/admin/lista/creador', [ListaController::class, 'create'])->name('adm.lista-creador');
    Route::post('/admin/lista/store', [ListaController::class, 'store'])->name('adm.lista-store');
    Route::get('/admin/lista/editor/{lista_id}', [ListaController::class, 'show'])->name('adm.lista-editor');
    Route::post('/admin/lista/update', [ListaController::class, 'update'])->name('adm.lista-update');
    Route::get('/admin/lista/destroy/{lista_id}', [ListaController::class, 'destroy'])->name('adm.lista-destroy');
    Route::get('/admin/lista/switch/{lista_id}', [ListaController::class, 'switch'])->name('adm.lista-switch');
    Route::post('/admin/lista/reordenar', [ListaController::class, 'reordenar'])->name('adm.lista-reordenar');

    // Info
    Route::get('/admin/info', [InfoController::class, 'show'])->name('adm.info');
    Route::post('/admin/info/update', [InfoController::class, 'update'])->name('adm.info-update');

    //newsletter
    Route::get('/admin/newsletter', [NewsletterController::class,'index'])->name('adm.newsletter');
    
    Route::post('/admin/newsletter/send', [NewsletterController::class,'send'])->name('adm.newsletter-send'); // NUEVA RUTA
    Route::get('/admin/newsletter/destroy/{newsletter_id}', [NewsletterController::class,'destroy'])->name('adm.newsletter-destroy');
    Route::get('/admin/newsletter/switch/{newsletter_id}', [NewsletterController::class,'switch'])->name('adm.newsletter-switch');
    Route::post('/admin/newsletter/reordenar', [NewsletterController::class, 'reordenar'])->name('adm.newsletter-reordenar');

    //logo
    Route::get('/admin/logo', [LogoController::class, 'show'])->name('adm.logo');
    Route::post('/admin/logo/update', [LogoController::class,'update'])->name('adm.logo-update');

    //usuarios
    Route::get('/admin/usuarios', [UsuarioController::class,'index'])->name('adm.usuarios');
    Route::get('/admin/usuarios/creador', [UsuarioController::class,'create'])->name('adm.usuarios-creador');
    Route::post('/admin/usuarios/store', [UsuarioController::class,'store'])->name('adm.usuarios-store');
    Route::get('/admin/usuarios/editor/{usuario_id}', [UsuarioController::class,'show'])->name('adm.usuarios-editor');
    Route::post('/admin/usuarios/update', [UsuarioController::class,'update'])->name('adm.usuarios-update');
    Route::get('/admin/usuarios/destroy/{usuario_id}', [UsuarioController::class,'destroy'])->name('adm.usuarios-destroy');
    Route::get('/admin/usuarios/switch/{usuario_id}', [UsuarioController::class,'switch'])->name('adm.usuarios-switch');

    //historico
    Route::get('/admin/pedidos', [PedidoController::class, 'adminIndex'])->name('adm.pedidos-historico');
    Route::get('/admin/pedidos/editor/{pedido}', [PedidoController::class, 'adminShow'])->name('adm.pedidos-historico-editor');
    Route::post('/admin/pedidos/update', [PedidoController::class, 'adminUpdate'])->name('adm.pedidos-historico-update');
    Route::get('/admin/pedidos/destroy/{pedido}', [PedidoController::class, 'adminDestroy'])->name('adm.pedidos-historico-destroy');
    Route::post('/admin/pedidos/estado/{pedido}', [PedidoController::class, 'adminEstado'])->name('adm.pedidos-historico-estado');

    //metadatos
    Route::get('/admin/metadatos', [MetadatoController::class,'index'])->name('adm.metadatos');
    Route::get('/admin/metadatos/creador', [MetadatoController::class,'create'])->name('adm.metadatos-creador');
    Route::post('/admin/metadatos/store', [MetadatoController::class,'store'])->name('adm.metadatos-store');
    Route::get('/admin/metadatos/editor/{metadato_id}', [MetadatoController::class,'show'])->name('adm.metadatos-editor');
    Route::post('/admin/metadatos/update', [MetadatoController::class,'update'])->name('adm.metadatos-update');
    Route::get('/admin/metadatos/destroy/{metadato_id}', [MetadatoController::class,'destroy'])->name('adm.metadatos-destroy');

    //área clientes: clientes (se crean solo desde acá, sin registro público)
    Route::get('/admin/clientes', [ClienteController::class, 'index'])->name('adm.clientes');
    Route::get('/admin/clientes/creador', [ClienteController::class, 'create'])->name('adm.clientes-creador');
    Route::post('/admin/clientes/store', [ClienteController::class, 'store'])->name('adm.clientes-store');
    Route::get('/admin/clientes/editor/{id}', [ClienteController::class, 'show'])->name('adm.clientes-editor');
    Route::post('/admin/clientes/update/{id}', [ClienteController::class, 'update'])->name('adm.clientes-update');
    Route::get('/admin/clientes/destroy/{id}', [ClienteController::class, 'destroy'])->name('adm.clientes-destroy');
    Route::get('/admin/clientes/switch/{id}', [ClienteController::class, 'switch'])->name('adm.clientes-switch');

    //banners (uno por sección: nosotros, novedades, contacto, productos, ...)
    Route::get('/admin/banner/{seccion}', [BannerController::class, 'show'])->name('adm.banner');
    Route::post('/admin/banner/{seccion}', [BannerController::class, 'update'])->name('adm.banner-update');

    //módulos generados con php artisan make:modulo
    require __DIR__.'/modulos.php';
});

//idioma
Route::get('/idioma/{idioma}', function ($idioma) {
    if (in_array($idioma, \App\Support\Idiomas::todos())) {
        session(['idioma' => $idioma]);
    }
    return back();
})->name('idioma.cambiar');


//login
Route::get('/admin/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class,'login'])->name('login.post');
Route::post('/admin/logout', [AuthController::class,'logout'])->name('logout');
Route::post('/admin/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

// ============================================
// WEB USUARIOS (ZONA PRIVADA)
// ============================================
Route::post('/web/register', [AuthController::class, 'webRegister'])->name('web.register');
Route::post('/web/login', [AuthController::class, 'webLogin'])->name('web.login');
Route::post('/web/logout', [AuthController::class, 'webLogout'])->name('web.logout');

// Ícono de usuario del header: login + registro de compradores (o "Mi cuenta" si ya inició sesión)
Route::get('/mi-cuenta', function () {
    return view('content.web.cuenta');
})->name('web.cuenta');

// ============================================
// ÁREA CLIENTES (modal de login, sin registro: los crea el admin)
// ============================================
Route::post('/area-clientes/login', [AuthController::class, 'clienteLogin'])->middleware('throttle:10,1')->name('cliente.login');
Route::post('/area-clientes/logout', [AuthController::class, 'clienteLogout'])->name('cliente.logout');

Route::middleware('cliente.auth')->group(function () {
    Route::get('/area-clientes/mis-productos/{producto?}', [AreaClientesController::class, 'productos'])->name('cliente.productos');
});

// ============================================
// TIENDA: carrito y compra (requieren cuenta; sin sesión llevan a "Mi cuenta")
// ============================================
Route::middleware('web.usuario.auth')->group(function () {
    Route::get('/carrito', [CarritoController::class, 'index'])->name('web-carrito');
    Route::get('/carrito/mini', [CarritoController::class, 'mini'])->name('web-carrito.mini');
    Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('web-carrito.agregar');
    Route::post('/carrito/actualizar', [CarritoController::class, 'actualizar'])->name('web-carrito.actualizar');
    Route::post('/carrito/quitar', [CarritoController::class, 'quitar'])->name('web-carrito.quitar');
    Route::post('/favoritos/{producto}', [CarritoController::class, 'favorito'])->name('web-favorito');

    Route::get('/compra', [CompraController::class, 'checkout'])->name('compra');
    Route::post('/compra', [CompraController::class, 'realizar'])->name('compra.realizar');
    Route::get('/compra/{pedido}/confirmado', [CompraController::class, 'confirmado'])->name('compra.confirmado');
    Route::get('/compra/{pedido}/mercado-pago', [CompraController::class, 'pagarMercadoPago'])->name('compra.mp.pagar');
    Route::get('/compra/{pedido}/mercado-pago/retorno', [CompraController::class, 'retornoMercadoPago'])->name('compra.mp.retorno');
    Route::post('/mi-cuenta/pedidos/{pedido}/cancelar', [CompraController::class, 'cancelar'])->name('pedido.cancelar');
});

// notificaciones de pagos de Mercado Pago (sin sesión ni CSRF, ver bootstrap/app.php)
Route::post('/webhooks/mercadopago', [MercadoPagoController::class, 'webhook'])->name('mercadopago.webhook');

Route::post('/admin/newsletter/store', [NewsletterController::class,'store'])->name('adm.newsletter-store');
//WEB
Route::get('/', function () {
    return view('content.web.home');})->name('home');

// Páginas institucionales
Route::get('/nosotros', [InstitucionalController::class, 'nosotros'])->name('nosotros');
Route::get('/politica-de-privacidad', [InstitucionalController::class, 'privacidad'])->name('privacidad');
Route::get('/aplicaciones', [InstitucionalController::class, 'aplicaciones'])->name('aplicaciones');
Route::get('/nuestra-red', [InstitucionalController::class, 'nuestraRed'])->name('nuestra-red');
Route::get('/soporte-tecnico', [InstitucionalController::class, 'soporte'])->name('soporte');

// Post Venta y sus formularios (solo mandan mail a Dixter)
Route::get('/post-venta', [InstitucionalController::class, 'postVenta'])->name('post-venta');
Route::get('/post-venta/registra-tu-producto', [InstitucionalController::class, 'registro'])->name('post-venta.registro');
Route::post('/post-venta/registra-tu-producto', [InstitucionalController::class, 'enviarRegistro'])->middleware('throttle:5,1')->name('post-venta.registro.enviar');
Route::get('/post-venta/buzon-de-sugerencias', [InstitucionalController::class, 'sugerencias'])->name('post-venta.sugerencias');
Route::post('/post-venta/buzon-de-sugerencias', [InstitucionalController::class, 'enviarSugerencias'])->middleware('throttle:5,1')->name('post-venta.sugerencias.enviar');
Route::get('/post-venta/arrepentimiento-de-compra', [InstitucionalController::class, 'arrepentimiento'])->name('post-venta.arrepentimiento');
Route::post('/post-venta/arrepentimiento-de-compra', [InstitucionalController::class, 'enviarArrepentimiento'])->middleware('throttle:5,1')->name('post-venta.arrepentimiento.enviar');

// Catálogo
Route::get('/productos', [CatalogoController::class, 'index'])->name('productos');
Route::get('/productos/{producto}', [CatalogoController::class, 'show'])->whereNumber('producto')->name('productos.detalle');

Route::get('/novedades', function () {
    return view('content.web.novedade');})->name('novedades');

Route::get('/novedades/detalle/{id}', [NovedadeController::class, 'detalle'])->name('novedades-detalle');

Route::get('/contacto', [InstitucionalController::class, 'contacto'])->name('contacto');


Route::post('/contacto/enviar', [FormController::class, 'contacto'])->middleware('throttle:5,1')->name('form.contacto');
