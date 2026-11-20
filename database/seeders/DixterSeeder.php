<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Banner;
use App\Models\Compromiso;
use App\Models\Contacto;
use App\Models\Distribuidore;
use App\Models\Familia;
use App\Models\Soporte;
use App\Models\Home;
use App\Models\HomeBanner;
use App\Models\Info;
use App\Models\Logo;
use App\Models\Nosotro;
use App\Models\Postventa;
use App\Models\Producto;
use App\Models\ProductoCliente;
use App\Models\Sectore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Contenido inicial de Dixter 2.0 (textos e imágenes del Figma).
 * Se puede correr varias veces: actualiza en lugar de duplicar.
 *
 *   php artisan db:seed --class=DixterSeeder
 *
 * Cliente de prueba del Área clientes: usuario "marianor", contraseña "dixter2026".
 */
class DixterSeeder extends Seeder
{
    private const IMG = '/assets/img/dixter/';

    public function run(): void
    {
        Logo::updateOrCreate(['id' => 1], [
            'foto_nav' => self::IMG . 'logo.png',
            'foto_footer' => self::IMG . 'logo.png',
        ]);

        $contacto = Contacto::firstOrNew(['id' => 1]);
        $contacto->forceFill([
            'latitud' => -34.61685,
            'longitud' => -58.41425,
            'direccion' => "Venezuela 3557 CABA.\nBuenos Aires, Argentina.",
            'mail' => 'ventas@dixter.com.ar',
            'telefono' => '+54 11 4931-7322',
            'whatssap' => '+54 911 4180 7294',
            'facebook' => 'https://www.facebook.com/',
            'instagram' => 'https://www.instagram.com/',
            'youtube' => 'https://www.youtube.com/',
        ])->save();

        HomeBanner::updateOrCreate(['id' => 1], [
            'orden' => 'AA',
            'foto' => self::IMG . 'hero-home.png',
            'tipo_medio' => 'imagen',
            'titulo' => 'Equipos para profesionales.',
            'texto' => 'Aspiradoras, lavadoras, pulidoras y turbinas con garantía de fábrica y servicio técnico en todo el país.',
            'active' => true,
        ]);

        $home = Home::firstOrNew(['id' => 1]);
        $home->forceFill([
            'soporte_etiqueta' => 'Soporte técnico',
            'soporte_titulo' => 'Tu equipo, siempre en marcha.',
            'soporte_texto' => 'Respaldamos la continuidad de tu producción. Contamos con un equipo técnico capacitado para brindarte asistencia rápida, mantenimiento preventivo y repuestos originales.',
            'postventa_etiqueta' => 'Post venta',
            'postventa_titulo' => 'Estamos con vos después de la compra.',
            'postventa_texto' => 'Respaldamos la continuidad de tu producción. Contamos con un equipo técnico capacitado para brindarte asistencia rápida, mantenimiento preventivo y repuestos originales.',
            'familias_etiqueta' => 'Productos',
            'familias_titulo' => 'Nuestras familias de productos',
            'beneficios' => [
                ['destacado' => 'Técnicos especializados', 'texto' => 'de fábrica, en todo el país'],
                ['destacado' => 'Repuestos originales', 'texto' => 'en stock permanente'],
                ['destacado' => 'Respuesta en 48 hs', 'texto' => 'para servicio técnico'],
            ],
            'lema' => 'Vendemos equipos hechos para durar.',
            'lema_destacado' => 'Te acompañamos para que así sea.',
            'sectores_etiqueta' => 'Industrias',
            'sectores_titulo' => 'Equipos pensados para cada entorno de trabajo.',
        ])->save();

        $nosotros = Nosotro::firstOrNew(['id' => 1]);
        $nosotros->forceFill([
            'foto' => self::IMG . 'nosotros-fachada.png',
            'texto1' => 'Desde 1947, fabricando en Argentina.',
            'descripcion' => '<p>Nacida en 1947, basamos nuestra actividad en el diseño, producción y comercialización de máquinas y sistemas para limpieza industrial.</p>'
                . '<p>Con el transcurso de los años y conociendo cada vez más las necesidades del mercado, fuimos incorporando nuevos diseños en la línea de maquinaria de limpieza, pulidoras de piso y turbinas y secadores para brindar una mayor satisfacción a nuestros clientes.</p>'
                . '<p>Diseñamos y fabricamos más de 200 modelos de aspiradoras de uso profesional e industrial para resolver todo tipo de trabajo de limpieza.</p>'
                . '<p>Contamos con la línea más completa de pulidoras de pisos duros, ampliamente probadas en el mercado Argentino, porque son robustos, potentes, y de construcción industrial.</p>',
        ])->save();

        $familias = [
            ['Aspiradoras', 'Aspiradoras profesionales e industriales para polvo y líquido, pensadas para trabajo continuo.', 'familia-aspiradoras.png'],
            ['Lavadoras de pisos', 'Lavado y secado en una sola pasada para grandes superficies, con autonomía y bajo consumo de agua.', 'familia-lavadoras.png'],
            ['Pulidoras de pisos', 'La línea más completa de pulidoras para pisos duros: robustas, potentes y de construcción industrial.', 'familia-pulidoras.png'],
            ['Turbinas y Secadores', 'Turbinas y secadores de alto caudal para un secado rápido de pisos, alfombras y tapizados.', 'familia-turbinas.png'],
        ];
        foreach ($familias as $i => [$nombre, $descripcion, $foto]) {
            Familia::updateOrCreate(['nombre' => $nombre], [
                'orden' => $this->orden($i),
                'descripcion' => $descripcion,
                'foto' => self::IMG . $foto,
                'active' => true,
                'destacado' => true,
            ]);
        }

        $sectores = [
            ['Logística y depósitos', 'Mantenimiento y limpieza profunda en grandes superficies, naves industriales y zonas de alto tránsito de autoelevadores.', 'sector-warehouse.svg'],
            ['Alimentos y bebidas', 'Soluciones de higienización rigurosa que garantizan entornos sanitizados en plantas procesadoras, cocinas y zonas de fraccionamiento.', 'sector-utensils.svg'],
            ['Salud', 'Equipos de aspiración y lavado de alto rendimiento para cumplir con los estándares críticos de desinfección en clínicas y hospitales.', 'sector-activity.svg'],
            ['Hotelería', 'Insumos y maquinaria silenciosa para la limpieza de alfombras, moquettes, áreas comunes y habitaciones con acabado impecable.', 'sector-hotel.svg'],
            ['Transporte', 'Limpieza intensiva de flotas, lavaderos, terminales de pasajeros y talleres con equipos de agua a presión y trabajo pesado.', 'sector-bus.svg'],
        ];
        foreach ($sectores as $i => [$nombre, $descripcion, $icono]) {
            Sectore::updateOrCreate(['nombre' => $nombre], [
                'orden' => $this->orden($i),
                'descripcion' => $descripcion,
                'icono' => self::IMG . $icono,
                'destacado' => true, // las 6 del Home
                'active' => true,
            ]);
        }

        // Productos de la tienda que usa el catálogo de ejemplo
        Producto::updateOrCreate(['codigo' => 'SK-550'], [
            'nombre' => 'Lavadora de pisos Super Kliner',
            'precio' => 1250000,
            'active' => true,
        ]);
        Producto::updateOrCreate(['codigo' => '2410'], [
            'nombre' => 'Aspiradora Profesional Super Vac 341-16',
            'precio' => 410000,
            'active' => true,
        ]);

        // Productos propios del Área clientes (no son los de la tienda)
        $pdf = $this->pdfDeEjemplo();
        $documentos = [
            ['titulo' => 'Guía rápida de puesta en marcha', 'archivo' => $pdf],
            ['titulo' => 'Manual de operación y mantenimiento', 'archivo' => $pdf],
            ['titulo' => 'Despiece modelo 6550', 'archivo' => $pdf],
            ['titulo' => 'Usá tu equipo correctamente: errores a evitar', 'archivo' => $pdf],
        ];
        ProductoCliente::updateOrCreate(['nombre' => 'Lavadora de pisos Super Kliner'], [
            'advertencia' => 'Leer el manual del equipo, transportar sobre las ruedas traseras y usar el producto limpiador recomendado.',
            'videos' => [],
            'documentos' => $documentos,
            'active' => true,
        ]);
        ProductoCliente::updateOrCreate(['nombre' => 'Aspiradora Profesional Super Vac 341-16'], [
            'advertencia' => 'Vaciar el tambor después de cada uso y limpiar el filtro de tela lavable periódicamente.',
            'videos' => [],
            'documentos' => array_slice($documentos, 0, 2),
            'active' => true,
        ]);

        Cliente::updateOrCreate(['usuario' => 'marianor'], [
            'nombre' => 'Mariano Rodríguez',
            'email' => 'mariano@example.com',
            'empresa' => 'Limpiezas del Sur',
            'contraseña' => Hash::make('dixter2026'),
            'active' => true,
        ]);

        $this->catalogo($pdf);
        $this->textosDeCompra();
        $this->institucional();
    }

    /** Nuestra red, Soporte técnico y Nosotros (logos de "¿Por qué elegirnos?" y compromiso) */
    private function institucional(): void
    {
        // [nombre, localidad, latitud, longitud, líneas]
        $distribuidores = [
            ['Distribuidor 1', 'Virrey del Pino', -34.8352, -58.6455, ['limpieza', 'turbinas', 'pulidoras', 'pulverizadores']],
            ['Distribuidor 2', 'Gregorio de Laferrere', -34.7472, -58.5781, ['limpieza']],
            ['Distribuidor 3', 'Morón', -34.6534, -58.6198, ['turbinas']],
            ['Distribuidor 4', 'Avellaneda', -34.6626, -58.3653, ['pulidoras']],
            ['Distribuidor 5', 'Adrogué', -34.7997, -58.3895, ['pulverizadores']],
        ];
        // Textos del encabezado de Post Venta
        Postventa::firstOrNew(['id' => 1])->forceFill(['etiqueta' => 'Post Venta', 'titulo' => 'Estamos con vos después de la compra.'])->save();

        // Título de la página (editable en Nuestra red > Distribuidores)
        Banner::seccion('nuestra-red')->update(['texto' => 'Nuestra red']);

        foreach ($distribuidores as $i => [$nombre, $localidad, $lat, $lng, $lineas]) {
            $n = $i + 1;
            Distribuidore::updateOrCreate(['nombre' => $nombre], [
                'orden' => $this->orden($i),
                'direccion' => 'Calle 123',
                'localidad' => $localidad,
                'provincia' => 'Buenos Aires',
                'web' => "https://www.distribuidor{$n}.com.ar",
                'email' => "info@distribuidor{$n}.com.ar",
                'telefono' => '+54 11 1234-5678',
                'latitud' => $lat,
                'longitud' => $lng,
                'lineas' => $lineas,
                'active' => true,
            ]);
        }

        $soporte = Soporte::firstOrNew(['id' => 1]);
        $soporte->forceFill([
            'etiqueta' => 'Soporte técnico',
            'titulo' => 'Tu equipo, siempre en marcha.',
            'texto' => 'Respaldamos la continuidad de tu producción. Contamos con un equipo técnico capacitado para brindarte asistencia rápida, mantenimiento preventivo y repuestos originales.',
            'items' => [
                ['icono' => self::IMG . 'soporte-wrench.svg', 'titulo' => 'Asesoramiento técnico', 'texto' => 'Puesta en marcha de equipos y entrenamiento a tus operadores para optimizar el uso de la tecnología desde el primer día.'],
                ['icono' => self::IMG . 'soporte-settings.svg', 'titulo' => 'Soporte post venta', 'texto' => 'Programas de revisión periódica para extender la vida útil de tus equipos, reduciendo al mínimo las paradas no programadas.'],
                ['icono' => self::IMG . 'soporte-boxes.svg', 'titulo' => 'Trazabilidad del producto', 'texto' => 'Disponibilidad de piezas de repuesto e insumos garantizados para asegurar la compatibilidad y el rendimiento de tu equipo.'],
                ['icono' => self::IMG . 'soporte-wrench.svg', 'titulo' => 'Repuestos de insumos originales', 'texto' => 'Diagnóstico preciso y resolución de problemas para mantener tus equipos operando al máximo rendimiento en todo momento.'],
            ],
            'cta_etiqueta' => 'Contactanos',
            'cta_titulo' => '¿Necesitas Soporte técnico para tu equipo?',
            'cta_boton' => 'Consultar',
        ])->save();

        // "¿Por qué elegirnos?": título 2 y logos 1 a 4 de Nosotros
        $logos = [
            ['pilar-shield.svg', 'Robustez', 'Equipos e insumos diseñados para resistir entornos industriales exigentes, garantizando máxima durabilidad y un rendimiento constante frente al uso intensivo.'],
            ['pilar-handshake.svg', 'Confianza', 'Años de trayectoria respaldando a nuestros clientes con asesoramiento transparente, soporte técnico especializado y relaciones comerciales sólidas a largo plazo.'],
            ['pilar-target.svg', 'Fiabilidad', 'Equipos que cumplen exactamente con lo prometido para asegurar la continuidad de tu trabajo, día tras día.'],
            ['pilar-sprout.svg', 'Sustentabilidad', 'Compromiso con la eficiencia energética y la reducción de residuos en nuestros procesos e insumos, promoviendo una producción industrial más responsable.'],
        ];
        $datosLogos = ['texto2' => '¿Por qué elegirnos?'];
        foreach ($logos as $i => [$icono, $titulo, $texto]) {
            $n = $i + 1;
            $datosLogos["icono{$n}"] = self::IMG . $icono;
            $datosLogos["nombre_icono{$n}"] = $titulo;
            $datosLogos["texto_icono{$n}"] = $texto;
        }
        Nosotro::where('id', 1)->update($datosLogos);

        // El orden define la posición en el diagrama: izquierda, derecha, arriba, abajo izquierda, abajo derecha
        $compromisos = [
            ['compromiso-servicio.svg', 'Servicio técnico', 'Técnicos propios formados en fábrica para diagnóstico, reparación y mantenimiento preventivo.'],
            ['compromiso-postventa.svg', 'Post venta', 'Te acompañamos después de la compra: capacitación, seguimiento y asistencia cuando la necesites.'],
            ['compromiso-repuestos.svg', 'Repuestos originales', 'Repuestos y accesorios originales para que tu equipo rinda como el primer día.'],
            ['compromiso-garantia.svg', 'Garantía de fábrica', 'Todos nuestros equipos cuentan con garantía directa de fábrica.'],
            ['compromiso-fabricacion.svg', 'Fabricación nacional', 'Diseñamos y fabricamos en Argentina desde 1947.'],
        ];
        foreach ($compromisos as $i => [$icono, $titulo, $texto]) {
            Compromiso::updateOrCreate(['titulo' => $titulo], ['orden' => $this->orden($i), 'icono' => self::IMG . $icono, 'texto' => $texto, 'active' => true]);
        }

        // Texto completo de Nosotros (el Home muestra el mismo contenido)
        Nosotro::where('id', 1)->update(['descripcion' => '<p>Nacida en 1947, basamos nuestra actividad en el diseño, producción y comercialización de máquinas y sistemas para limpieza industrial.</p>'
            . '<p>Con el transcurso de los años y conociendo cada vez más las necesidades del mercado, fuimos incorporando nuevos diseños en la línea de maquinaria de limpieza, pulidoras de piso y turbinas y secadores para brindar una mayor satisfacción a nuestros clientes.</p>'
            . '<p>Diseñamos y fabricamos más de 200 modelos de aspiradoras de uso profesional e industrial para resolver todo tipo de trabajo de limpieza.</p>'
            . '<p>Contamos con la línea más completa de pulidoras de pisos duros, ampliamente probadas en el mercado Argentino, porque son robustos, potentes, y de construcción industrial.</p>'
            . '<p>Brindamos herramientas de calidad desde el comienzo de la peluquería de mascotas en nuestro país con una gama completa de turbinas y secadores. Hoy, con más de medio siglo de historia y tres generaciones en Argentina, seguimos ofreciendo productos de alta calidad para un mercado altamente competitivo.</p>']);

        // Industrias del Figma de Aplicaciones que no estaban en el Home
        foreach ([
            ['Peluquería canina', 'Aspiradoras de alta potencia de sólidos y líquidos para la eliminación eficiente de pelos, agua y desinfección de superficies.', 'sector-paw.svg'],
            ['Car detailing', 'Limpieza intensiva de flotas, lavaderos, terminales de pasajeros y talleres con equipos de agua a presión y trabajo pesado.', 'sector-car.svg'],
        ] as $i => [$nombre, $descripcion, $icono]) {
            Sectore::updateOrCreate(['nombre' => $nombre], ['orden' => 'B' . chr(ord('A') + $i), 'descripcion' => $descripcion, 'icono' => self::IMG . $icono, 'active' => true]);
        }
    }

    /** Árbol de categorías y productos del Figma (Producto A / Producto C) */
    private function catalogo(string $pdf): void
    {
        $crear = function (string $nombre, ?Categoria $padre, int $i) {
            return Categoria::updateOrCreate(
                ['nombre' => $nombre, 'parent_id' => $padre?->id],
                ['orden' => $this->orden($i), 'active' => true]
            );
        };

        $maquinas = $crear('Máquinas de limpieza', null, 0);
        $aspiradoras = $crear('Aspiradoras', $maquinas, 0);
        $serie300 = $crear('Profesional Serie 300', $aspiradoras, 0);
        foreach (['Portátil Sopladora', 'Industrial Serie 400', 'Classic Uso Continuo', 'Accesorios y repuestos', 'Con Filtros Especiales', 'Voltajes Especiales'] as $i => $nombre) {
            $crear($nombre, $aspiradoras, $i + 1);
        }
        $crear('Lavatapizados', $maquinas, 1);
        $lavadoras = $crear('Lavadora de pisos', $maquinas, 2);
        $crear('Barredoras de piso', $maquinas, 3);
        foreach (['Turbinas y Secadores', 'Pulidoras de pisos', 'Pulverizadores'] as $i => $nombre) {
            $crear($nombre, null, $i + 1);
        }

        $descripcion = '<p><strong>Materiales altamente resistentes</strong></p>'
            . '<p>Tambor de polietileno de 6mm de espesor. No se abolla, no se despinta ni se oxida. Resistente a los químicos. Da una larga vida sin fallas.<br>Cable de tipo industrial con pasacable reforzado y ficha normalizada.</p>'
            . '<p><strong>Motores de gran potencia</strong></p>'
            . '<p>Motores industriales robustos de alta potencia. Poseen ventilación independiente para evitar que el aire húmedo pase a través del mismo</p>'
            . '<p><strong>Filtro de tela lavable</strong></p>'
            . '<p>Poseen filtro de tela re-utilizable de fácil lavado. Tiene un poder de filtrado capaz de retener partículas de un tamaño de hasta 5 micrones.</p>'
            . '<p><strong>Accesorios</strong></p>'
            . '<ul><li>Caño de 36 mm en tres secciones con regulador de entrada de aire</li><li>Manguera de dos metros</li><li>Boquilla para pisos y alfombras</li><li>Cepillo redondo</li><li>Boquilla rinconera</li><li>Boquilla para tapizados</li></ul>';

        $ficha = fn (array $v) => array_map(
            fn ($nombre, $valor) => ['nombre' => $nombre, 'valor' => $valor],
            ['Modelo', 'Consumo', 'Voltaje', 'Succión', 'Caudal', 'Capacidad', 'Aspiración', 'Ruedas', 'Peso', 'Cable'],
            $v
        );

        // [código, nombre, precio, precio anterior, foto, ficha]
        $productos = [
            ['2410', 'Aspiradora Profesional Super Vac 341-16', 410000, null, 'producto-341-16.png', $ficha(['341-16', '1200 W', '220V', '2360 mm H20', '3 m/min', '16 litros', 'Polvo', '4 giratorias', '7,5 kg', '5 metros'])],
            ['2210', 'Aspiradora Profesional Super Vac 321-16', 348500, 498999, 'producto-321-16.png', $ficha(['321-16', '1000 W', '220V', '2200 mm H20', '2.7 m/min', '16 litros', 'Polvo y líquidos', '4 giratorias', '7,5 kg', '5 metros'])],
            ['2420', 'Aspiradora Profesional Super Vac 341-24', 515000, null, 'producto-341-24.png', $ficha(['341-24', '1200 W', '220V', '2840 mm H20', '3.1 m/min', '24 litros', 'Polvo', '4 giratorias', '10 kg', '10 metros'])],
            ['2220', 'Aspiradora Profesional Super Vac 321-24', 348500, 560000, 'producto-321-24.png', $ficha(['321-24', '1000 W', '220V', '2200 mm H20', '2.7 m/min', '24 litros', 'Polvo y líquidos', '4 giratorias', '10 kg', '10 metros'])],
            ['2211', 'Aspiradora Profesional Super Vac 321-40', 348500, 619000, 'producto-321-40.png', null],
            ['2231', 'Aspiradora Profesional Super Vac 321-40C', null, null, 'familia-aspiradoras.png', null],
            ['2241', 'Aspiradora Profesional Super Vac 321-60C', null, null, 'producto-341-24.png', null],
        ];

        foreach ($productos as $i => [$codigo, $nombre, $precio, $anterior, $foto, $fichaProducto]) {
            $producto = Producto::updateOrCreate(['codigo' => $codigo], [
                'categoria_id' => $serie300->id,
                'orden' => $this->orden($i),
                'nombre' => $nombre,
                'precio' => $precio,
                'precio_anterior' => $anterior,
                'iva' => 21,
                'foto' => self::IMG . $foto,
                'descripcion' => $descripcion,
                'ficha' => $fichaProducto,
                'informacion' => [['nombre' => 'Peso', 'valor' => '8 kg'], ['nombre' => 'Dimensiones', 'valor' => '40 × 40 × 60 cm']],
                'instructivos' => [['titulo' => 'Guía rápida de puesta en marcha', 'archivo' => $pdf]],
                'active' => true,
            ]);

            if ($codigo === '2410' && $producto->fotos()->count() === 0) {
                foreach (range(2, 5) as $n) {
                    $producto->fotos()->create(['orden' => $this->orden($n), 'foto' => self::IMG . "producto-galeria-{$n}.png", 'active' => true]);
                }
            }
        }

        // El producto "prueba" del seeder base no tiene foto ni categoría: no se muestra en la web
        Producto::where('codigo', '1ab')->update(['active' => false]);

        Producto::where('codigo', 'SK-550')->update(['categoria_id' => $lavadoras->id, 'iva' => 21, 'foto' => self::IMG . 'familia-lavadoras.png']);

        // Las familias del Home llevan a su categoría del catálogo
        Familia::where('nombre', 'Aspiradoras')->update(['link' => route('productos', ['categoria' => $aspiradoras->id], false)]);
        Familia::where('nombre', 'Lavadoras de pisos')->update(['link' => route('productos', ['categoria' => $lavadoras->id], false)]);
        foreach (['Pulidoras de pisos', 'Turbinas y Secadores'] as $nombre) {
            $categoria = Categoria::where('nombre', $nombre)->whereNull('parent_id')->first();
            Familia::where('nombre', $nombre)->update(['link' => route('productos', ['categoria' => $categoria->id], false)]);
        }
    }

    private function textosDeCompra(): void
    {
        $info = Info::firstOrNew(['id' => 1]);
        $info->forceFill([
            'retiro_texto' => "Venezuela 3557 CABA. Buenos Aires, Argentina.\nAnte cualquier consulta contactarse con +54 911 4180 7294 o ventas@dixter.com.ar\nHorario de atención de 8:30 - 12 hs y de 13 - 17 hs",
            'pago_local_texto' => "Consultar por cuotas y promociones vigentes.\nPara recibir información contactarse con +54 911 4180 7294 o ventas@dixter.com.ar\nHorario de atención de 8:30 - 12 hs y de 13 - 17 hs",
            'cuotas_texto' => "Consultar por cuotas y promociones vigentes.\nPara recibir información contactarse con +54 911 4180 7294 o ventas@dixter.com.ar",
            'mail_ventas' => $info->mail_ventas ?: 'ventas@dixter.com.ar',
        ])->save();

        // Texto de ejemplo de la Política de privacidad (se reemplaza desde el admin)
        if (blank($info->privacidad)) {
            $info->forceFill(['privacidad' => '<p>En Dixter respetamos la privacidad de quienes visitan este sitio y compran nuestros productos.</p>'
                . '<h2>Datos que recopilamos</h2><p>Nombre, apellido, email, teléfono, empresa y dirección de entrega que nos brindás al registrarte o al comprar.</p>'
                . '<h2>Para qué los usamos</h2><ul><li>Procesar y entregar tus pedidos.</li><li>Administrar tu cuenta.</li><li>Responder tus consultas.</li></ul>'
                . '<h2>Tus derechos</h2><p>Podés pedir el acceso, la rectificación o la eliminación de tus datos escribiendo a ventas@dixter.com.ar.</p>'])->save();
        }
    }

    /** Orden con letras (AA, AB, ...) como el drag & drop del admin */
    private function orden(int $i): string
    {
        return 'A' . chr(ord('A') + $i);
    }

    /** PDF mínimo para que "Descargar" funcione con datos de ejemplo */
    private function pdfDeEjemplo(): string
    {
        $ruta = 'productos/documentos/ejemplo-manual.pdf';

        if (!Storage::disk('public')->exists($ruta)) {
            $contenido = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
                . "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n"
                . "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n"
                . "4 0 obj<</Length 58>>stream\nBT /F1 18 Tf 72 760 Td (Dixter - Manual de ejemplo) Tj ET\nendstream endobj\n"
                . "5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n"
                . "trailer<</Root 1 0 R>>\n%%EOF";
            Storage::disk('public')->put($ruta, $contenido);
        }

        return Storage::url($ruta);
    }
}
