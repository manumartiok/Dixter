<?php

namespace App\Http\Controllers;

use App\Mail\FormularioMail;
use App\Models\Banner;
use App\Models\Compromiso;
use App\Models\Contacto;
use App\Models\Distribuidore;
use App\Models\Home;
use App\Models\Info;
use App\Models\Nosotro;
use App\Models\Postventa;
use App\Models\Producto;
use App\Models\Sectore;
use App\Models\Soporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Páginas institucionales: Aplicaciones, Nuestra red, Soporte técnico, Post Venta
 * (con sus 3 formularios, que solo envían mail), Nosotros y Contacto.
 */
class InstitucionalController extends Controller
{
    /** Opciones del Buzón de sugerencias (Figma: Post Venta B) */
    public const SUGERENCIAS = [
        'donde' => ['Sitio web de Dixter', 'Local de Dixter', 'Distribuidor', 'Mercado Libre', 'Otro'],
        'calificacion' => ['Excelente', 'Muy bueno', 'Bueno', 'Regular', 'Malo'],
        'motivo' => ['Calidad de los equipos', 'Precio', 'Recomendación', 'Servicio técnico y repuestos', 'Trayectoria de la marca', 'Otro'],
        'medio' => ['Google / internet', 'Redes sociales', 'Recomendación', 'Distribuidor', 'Exposición o feria', 'Otro'],
        'recomendaria' => ['Sí', 'No', 'Tal vez'],
    ];

    public function aplicaciones()
    {
        return view('content.web.aplicaciones', [
            'home' => Home::first(),
            'sectores' => Sectore::where('active', true)->orderBy('orden')->get(),
        ]);
    }

    public function nuestraRed(Request $request)
    {
        $todos = Distribuidore::where('active', true)->orderBy('orden')->orderBy('nombre')->get();

        $filtrados = $todos
            ->when($request->filled('provincia'), fn ($c) => $c->where('provincia', $request->provincia))
            ->when($request->filled('localidad'), fn ($c) => $c->filter(fn ($d) => str_contains(mb_strtolower((string) $d->localidad), mb_strtolower($request->localidad))))
            ->when($request->filled('linea'), fn ($c) => $c->filter(fn ($d) => in_array($request->linea, $d->lineas ?? [])))
            ->values();

        return view('content.web.nuestra-red', [
            'distribuidores' => $filtrados,
            'titulo' => Banner::seccion('nuestra-red')->t('texto') ?: 'Nuestra red',
            'provincias' => $todos->pluck('provincia')->filter()->unique()->sort()->values(),
        ]);
    }

    public function soporte()
    {
        return view('content.web.soporte', ['soporte' => Soporte::first()]);
    }

    public function postVenta()
    {
        return view('content.web.post-venta', ['textos' => Postventa::first()]);
    }

    public function nosotros()
    {
        return view('content.web.nosotros', [
            'nosotros' => Nosotro::first(),
            'compromisos' => Compromiso::where('active', true)->orderBy('orden')->take(5)->get(),
        ]);
    }

    public function privacidad()
    {
        return view('content.web.privacidad', ['info' => Info::first()]);
    }

    public function contacto()
    {
        return view('content.web.contacto', ['contacto' => Contacto::first()]);
    }

    // ------------------------------------------------------------------
    // Formularios de Post Venta (solo mail a Dixter)
    // ------------------------------------------------------------------

    public function registro()
    {
        return view('content.web.post-venta-registro');
    }

    public function enviarRegistro(Request $request)
    {
        $datos = $request->validate([
            'modelo' => 'required|string|max:255',
            'numero_serie' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'celular' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'empresa' => 'required|string|max:255',
            'rubro' => 'required|string|max:255',
        ], $this->mensajes());

        return $this->enviar('Registro de producto', [
            'Modelo' => $datos['modelo'],
            'Número de serie' => $datos['numero_serie'],
            'Nombre' => $datos['nombre'] . ' ' . $datos['apellido'],
            'E-mail' => $datos['email'],
            'Celular' => $datos['celular'] ?? '',
            'Empresa' => $datos['empresa'],
            'Rubro' => $datos['rubro'],
        ], $datos['email'], 'post-venta.registro', 'Registramos tu producto. Te vamos a escribir para activar la garantía.');
    }

    public function sugerencias()
    {
        return view('content.web.post-venta-sugerencias', [
            'opciones' => self::SUGERENCIAS,
            'equipos' => Producto::activos()->orderBy('nombre')->pluck('nombre'),
        ]);
    }

    public function enviarSugerencias(Request $request)
    {
        $o = self::SUGERENCIAS;
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'equipo' => 'nullable|string|max:255',
            'donde' => 'required|in:' . implode(',', $o['donde']),
            'calificacion' => 'required|in:' . implode(',', $o['calificacion']),
            'motivo' => 'required|in:' . implode(',', $o['motivo']),
            'medio' => 'required|in:' . implode(',', $o['medio']),
            'recomendaria' => 'nullable|in:' . implode(',', $o['recomendaria']),
            'comentario' => 'nullable|string|max:3000',
        ], $this->mensajes());

        return $this->enviar('Buzón de sugerencias', [
            'Nombre' => $datos['nombre'] . ' ' . $datos['apellido'],
            'Equipo adquirido' => $datos['equipo'] ?? '',
            'Dónde compró' => $datos['donde'],
            'Calificación del asesoramiento' => $datos['calificacion'],
            'Motivo de compra' => $datos['motivo'],
            'Cómo conoció la marca' => $datos['medio'],
            '¿Recomendaría los productos?' => $datos['recomendaria'] ?? '',
            'Comentarios' => $datos['comentario'] ?? '',
        ], null, 'post-venta.sugerencias', '¡Gracias! Recibimos tus sugerencias.');
    }

    public function arrepentimiento()
    {
        return view('content.web.post-venta-arrepentimiento');
    }

    public function enviarArrepentimiento(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'celular' => 'nullable|string|max:50',
            'numero_pedido' => 'required|string|max:50',
            'fecha_compra' => 'required|date|before_or_equal:today',
            'motivo' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:3000',
            'adjunto' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx',
        ], $this->mensajes() + [
            'fecha_compra.before_or_equal' => 'La fecha de compra no puede ser futura.',
            'adjunto.max' => 'El archivo no puede superar los 10MB.',
            'adjunto.mimes' => 'Adjuntá una imagen, un PDF o un documento de Word.',
        ]);

        return $this->enviar('Arrepentimiento de compra', [
            'Nombre' => $datos['nombre'] . ' ' . $datos['apellido'],
            'E-mail' => $datos['email'],
            'Celular' => $datos['celular'] ?? '',
            'Número de pedido' => $datos['numero_pedido'],
            'Fecha de compra' => \Carbon\Carbon::parse($datos['fecha_compra'])->format('d/m/Y'),
            'Motivo de la devolución' => $datos['motivo'] ?? '',
            'Descripción del problema' => $datos['descripcion'] ?? '',
        ], $datos['email'], 'post-venta.arrepentimiento', 'Recibimos tu solicitud de arrepentimiento. Te vamos a contactar a la brevedad.', $request->file('adjunto'));
    }

    // ------------------------------------------------------------------

    /** Envía el mail a Dixter (respuesta al remitente, si lo hay) y vuelve al formulario */
    private function enviar(string $asunto, array $campos, ?string $responderA, string $ruta, string $ok, $adjunto = null)
    {
        $destino = Contacto::first()?->mail;

        try {
            if (!$destino) {
                throw new \RuntimeException('Falta el mail de contacto en el administrador.');
            }
            Mail::to($destino)->send(new FormularioMail($asunto, $campos, $responderA, $adjunto));
        } catch (Throwable $e) {
            Log::error("No se pudo enviar el formulario \"{$asunto}\": " . $e->getMessage());

            return redirect()->route($ruta)->withInput()->with('error_formulario', 'No pudimos enviar el formulario. Intentá nuevamente o escribinos por WhatsApp.');
        }

        return redirect()->route($ruta)->with('ok_formulario', $ok);
    }

    private function mensajes(): array
    {
        return [
            'required' => 'Completá este dato.',
            'email' => 'Ingresá un e-mail válido.',
            'in' => 'Elegí una opción de la lista.',
            'date' => 'Ingresá una fecha válida.',
        ];
    }
}
