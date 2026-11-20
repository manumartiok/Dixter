<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protege el "Área clientes". Sin sesión vuelve al home y abre el modal de login.
 */
class ClienteAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('cliente')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
            }

            session(['cliente_intended_url' => $request->url()]);

            return redirect()->route('home')->with('abrir_modal_clientes', true);
        }

        if (!Auth::guard('cliente')->user()->active) {
            Auth::guard('cliente')->logout();

            return redirect()->route('home')->with('abrir_modal_clientes', true);
        }

        return $next($request);
    }
}
