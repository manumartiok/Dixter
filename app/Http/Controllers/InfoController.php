<?php

namespace App\Http\Controllers;

use App\Models\Info;
use Illuminate\Http\Request;

/**
 * Admin: textos que se muestran en el carrito, la compra y el detalle del producto,
 * el texto completo de la Política de privacidad (/politica-de-privacidad)
 * y las casillas que reciben los avisos de pedidos.
 */
class InfoController extends Controller
{
    private const CAMPOS = ['retiro_texto', 'pago_local_texto', 'cuotas_texto', 'privacidad', 'mail_ventas', 'mail_ventas_cc'];

    public function show()
    {
        return view('content.admin.info', ['info' => Info::first() ?? new Info()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'retiro_texto' => 'nullable|string|max:2000',
            'pago_local_texto' => 'nullable|string|max:2000',
            'cuotas_texto' => 'nullable|string|max:2000',
            'privacidad' => 'nullable|string', // longText: sin límite práctico
            'mail_ventas' => 'nullable|email|max:255',
            'mail_ventas_cc' => 'nullable|email|max:255',
        ]);

        $info = Info::first() ?? new Info();
        foreach (self::CAMPOS as $campo) {
            $info->{$campo} = $request->input($campo);
        }
        $info->save();

        return redirect()->route('adm.info')->with('success', 'Información actualizada correctamente.');
    }
}
