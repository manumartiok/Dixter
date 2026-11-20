<?php

namespace App\Http\Controllers;

use App\Mail\FormularioMail;
use App\Models\Contacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class FormController extends Controller
{
    /** Formulario "Hablemos" de Contacto (Figma: Contacto). Responde JSON. */
    public function contacto(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'empresa' => 'nullable|string|max:255',
            'mensaje' => 'required|string|max:3000',
        ], [
            'required' => 'Completá los datos obligatorios.',
            'email' => 'Ingresá un e-mail válido.',
        ]);

        $destino = Contacto::first()?->mail;

        try {
            if (!$destino) {
                throw new \RuntimeException('Falta el mail de contacto en el administrador.');
            }

            Mail::to($destino)->send(new FormularioMail('Consulta desde Contacto', [
                'Nombre y apellido' => $datos['nombre'],
                'E-mail' => $datos['email'],
                'Teléfono' => $datos['telefono'] ?? '',
                'Empresa' => $datos['empresa'] ?? '',
                'Mensaje' => $datos['mensaje'],
            ], $datos['email']));
        } catch (Throwable $e) {
            Log::error('No se pudo enviar el formulario de contacto: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la consulta. Por favor, intentá nuevamente.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tu consulta fue enviada. Te respondemos a la brevedad.',
        ]);
    }
}
