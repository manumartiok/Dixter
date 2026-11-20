<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WebUsuarioAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web_usuario')->check()) {
            // AJAX (agregar al carrito, favoritos): después del login vuelve a la página donde estaba
            if ($request->expectsJson()) {
                session(['intended_url' => url()->previous()]);
                session()->flash('aviso_login', 'Iniciá sesión o creá tu cuenta para comprar.');

                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado',
                    'redirect' => route('web.cuenta')
                ], 401);
            }

            // Guardar la URL a la que intentaba acceder y mandarlo a "Mi cuenta" (login + registro)
            session(['intended_url' => $request->fullUrl()]);

            return redirect()->route('web.cuenta')->with('aviso_login', 'Iniciá sesión o creá tu cuenta para comprar.');
        }

        return $next($request);
    }
}