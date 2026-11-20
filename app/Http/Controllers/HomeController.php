<?php

namespace App\Http\Controllers;

use App\Models\Home;
use Illuminate\Http\Request;

/**
 * Textos del Home. El hero se edita en "Home > Banner", las familias en
 * "Home > Familias de productos", las industrias en "Home > Industrias"
 * y el bloque Nosotros en "Nosotros > Contenido".
 */
class HomeController extends Controller
{
    private const CAMPOS = [
        'soporte_etiqueta', 'soporte_titulo', 'soporte_texto',
        'postventa_etiqueta', 'postventa_titulo', 'postventa_texto',
        'familias_etiqueta', 'familias_titulo',
        'lema', 'lema_destacado',
        'sectores_etiqueta', 'sectores_titulo',
    ];

    public function show()
    {
        $home = Home::first() ?? new Home();

        return view('content.admin.home', compact('home'));
    }

    public function update(Request $request)
    {
        $reglas = array_fill_keys(self::CAMPOS, 'nullable|string|max:255');
        $reglas['soporte_texto'] = 'nullable|string|max:1000';
        $reglas['postventa_texto'] = 'nullable|string|max:1000';
        $reglas['beneficios'] = 'nullable|array|max:3';
        $reglas['beneficios.*.destacado'] = 'nullable|string|max:255';
        $reglas['beneficios.*.texto'] = 'nullable|string|max:255';

        $request->validate($reglas);

        $home = Home::first() ?? new Home();

        foreach (self::CAMPOS as $campo) {
            $home->{$campo} = $request->input($campo);
        }

        // Los 3 beneficios tienen ícono animado fijo (técnicos, repuestos, respuesta): solo se editan los textos
        $home->beneficios = collect(range(0, 2))->map(fn ($i) => [
            'destacado' => $request->input("beneficios.{$i}.destacado"),
            'texto' => $request->input("beneficios.{$i}.texto"),
        ])->all();

        $home->save();

        return redirect()->route('adm.home')->with('success', 'Página de inicio actualizada correctamente.');
    }
}
