<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\WebUsuario;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLogin(Request $request)
    {
        // Si venimos de un logout exitoso, podemos mostrar un mensaje
        $logoutMessage = session('logout_message');
        return view('auth.login', compact('logoutMessage'));
    }

    // Procesar login
    public function login(Request $request)
    {
        $request->validate([
            'usuario' => 'required',
            'contraseña' => 'required',
        ]);

        $usuario = Usuario::where('usuario', $request->usuario)
                          ->orWhere('email', $request->usuario)
                          ->first();

        if (!$usuario) {
            return back()->withErrors([
                'usuario' => 'Usuario o email incorrecto',
            ]);
        }

        if (!Hash::check($request->contraseña, $usuario->contraseña)) {
            return back()->withErrors([
                'usuario' => 'Contraseña incorrecta',
            ]);
        }

        if (!$usuario->active) {
            return back()->withErrors([
                'usuario' => 'Tu cuenta está inactiva. Contacta al administrador.',
            ]);
        }

        // Login exitoso
        Auth::login($usuario);
        return redirect()->route('adm.dashboard');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirigir al login con mensaje
        return redirect('/admin/login')->with('logout_message', 'Has cerrado sesión correctamente.');
    }

    // Forgot password
    public function forgotPassword(Request $request)
    {
        // Validación básica
        $request->validate([
            'email' => 'required|email|exists:usuarios,email', // ajustado a tu tabla
        ]);

        // Enviar link de reseteo
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        } else {
            return back()->with('error', __($status));
        }
    }

    // ============================================
    // WEB USUARIOS (ZONA PRIVADA)
    // ============================================
    
    public function webLogin(Request $request)
    {
        $request->validate([
            'usuario' => 'required',
            'contraseña' => 'required',
        ]);

        $usuario = WebUsuario::where('usuario', $request->usuario)
                         ->orWhere('email', $request->usuario)  // ← agregar esto
                         ->first();
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario incorrecto'
            ], 422);
        }

        if (!Hash::check($request->contraseña, $usuario->contraseña)) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña incorrecta'
            ], 422);
        }

        if (!$usuario->active) {
            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta está inactiva. Contacta al administrador.'
            ], 422);
        }

        // Login exitoso con guard específico
        Auth::guard('web_usuario')->login($usuario);
        $request->session()->regenerate();

        // Volver a donde estaba (p. ej. el carrito) o a "Mi cuenta"
        $intendedUrl = session()->pull('intended_url', route('web.cuenta'));

    return response()->json([
        'success' => true,
        'message' => 'Login exitoso',
        'redirect' => $intendedUrl
    ]);

    }

    public function webRegister(Request $request)
    {
        $validated = $request->validate([
            'usuario' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:web_usuarios'],
            'contraseña' => ['required', 'confirmed', PasswordRule::min(8)],
        ], [
            'usuario.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'contraseña.required' => 'La contraseña es obligatoria',
            'contraseña.confirmed' => 'Las contraseñas no coinciden',
            'contraseña.min' => 'La contraseña debe tener al menos 8 caracteres',
        ]);
        
        $usuario = WebUsuario::create([
            'usuario'      => $validated['usuario'],
            'email'        => $validated['email'],
            'contraseña'   => Hash::make($validated['contraseña']),
            // 'rol'          => null,
            'empresa'      => $request->empresa,
            'cuit'         => $request->cuit,
            'telefono'     => $request->telefono,
            'domicilio'    => $request->domicilio,
            'codigo_postal'=> $request->codigo_postal,
            'localidad'    => $request->localidad,
            'provincia'    => $request->provincia,
            // Compradores públicos: la cuenta queda activa al registrarse
            'active'       => true,
        ]);

        Auth::guard('web_usuario')->login($usuario);
        $request->session()->regenerate();


        // Después de crear el usuario, antes del return
try {
    $cuerpo = "
        <h2>Nuevo registro de usuario</h2>
        <p><strong>Usuario:</strong> {$usuario->usuario}</p>
        <p><strong>Email:</strong> {$usuario->email}</p>
        <p><strong>Empresa:</strong> {$request->empresa}</p>
        <p><strong>CUIT:</strong> {$request->cuit}</p>
        <p><strong>Teléfono:</strong> {$request->telefono}</p>
        <p><strong>Domicilio:</strong> {$request->domicilio}</p>
        <p><strong>Localidad:</strong> {$request->localidad}, {$request->provincia}, {$request->pais}</p>
        <p><strong>CP:</strong> {$request->codigo_postal}</p>
    ";

    Mail::html($cuerpo, function ($m) {
        $m->to('martimanuel912@gmail.com')
          ->subject('Nuevo usuario registrado');
    });
} catch (\Exception $e) {
    Log::error('Error mail nuevo usuario: ' . $e->getMessage());
}

        return response()->json([
            'success' => true,
            'message' => '¡Bienvenido! Tu cuenta fue creada.',
            'redirect' => session()->pull('intended_url', route('web.cuenta'))
        ]);
    }

    public function webLogout(Request $request)
    {
        Auth::guard('web_usuario')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('message', 'Has cerrado sesión correctamente');
    }

    // ============================================
    // ÁREA CLIENTES (modal, sin registro)
    // ============================================

    public function clienteLogin(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string',
            'contraseña' => 'required|string',
        ], [
            'usuario.required' => 'Ingresá tu nombre o mail.',
            'contraseña.required' => 'Ingresá tu contraseña.',
        ]);

        $ingreso = trim($request->usuario);
        $cliente = Cliente::where('usuario', $ingreso)
            ->orWhere('email', $ingreso)
            ->orWhere('nombre', $ingreso)
            ->get()
            ->first(fn ($c) => Hash::check($request->contraseña, $c->contraseña));

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'El nombre, mail o contraseña no son correctos.',
            ], 422);
        }

        if (!$cliente->active) {
            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta está inactiva. Contactate con Dixter.',
            ], 422);
        }

        Auth::guard('cliente')->login($cliente);
        $request->session()->regenerate();

        $redirect = session()->pull('cliente_intended_url', route('cliente.productos'));

        return response()->json(['success' => true, 'redirect' => $redirect]);
    }

    public function clienteLogout(Request $request)
    {
        Auth::guard('cliente')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}