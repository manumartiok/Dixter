<?php
namespace App\Http\Controllers;

use App\Models\WebUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WebUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WebUsuario::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('usuario', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%")
                    ->orWhere('empresa', 'like', "%{$q}%");
            });
        }

        $usuarios = $query->orderBy('id', 'desc')->paginate(50)->withQueryString();

        return view('content.admin.web-usuarios', ['usuarios' => $usuarios]);
    }

    /**
     * Display the specified resource.
     */
    public function show($usuarios_id)
    {
        $usuario = WebUsuario::find($usuarios_id);
        
        if (!$usuario) {
            return redirect()->route('adm.web-usuarios')->with('error', 'Usuario no encontrado.');
        }
        
        return view('content.admin.web-usuarios-editor', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Solo los datos del formulario de registro: nombre y apellido, email, celular y empresa.
     */
    public function update(Request $request)
    {
        $usuario = WebUsuario::findOrFail($request->input('usuarios_id'));

        $request->validate([
            'usuario'    => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:web_usuarios,email,' . $usuario->id,
            'telefono'   => 'nullable|string|max:255',
            'empresa'    => 'nullable|string|max:255',
            'contraseña' => 'nullable|min:8|confirmed',
        ], [
            'usuario.required'     => 'El nombre y apellido es obligatorio.',
            'email.required'       => 'El email es obligatorio.',
            'email.unique'         => 'Ese email ya está en uso por otro comprador.',
            'contraseña.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'contraseña.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $usuario->fill($request->only(['usuario', 'email', 'telefono', 'empresa']));

        if ($request->filled('contraseña')) {
            $usuario->contraseña = Hash::make($request->input('contraseña'));
        }

        $usuario->save();

        return redirect()->route('adm.web-usuarios')->with('success', 'Comprador actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($usuarios_id)
    {
        $usuario = WebUsuario::find($usuarios_id);
        
        if (!$usuario) {
            return redirect()->route('adm.web-usuarios')->with('error', 'Usuario no encontrado.');
        }
        
        $usuario->delete();
        return redirect()->route('adm.web-usuarios')->with('success', 'Usuario eliminado correctamente.');
    }
      
    /**
     * Toggle user active status.
     */
    public function switch($usuarios_id)
    {
        $usuario = WebUsuario::find($usuarios_id);

        if (!$usuario) {
            return redirect()->route('adm.web-usuarios')->with('error', 'Usuario no encontrado.');
        }

        $usuario->active = !$usuario->active;
        $usuario->save();

        $status = $usuario->active ? 'activado' : 'desactivado';
        return redirect()->route('adm.web-usuarios')->with('success', "Usuario {$status} correctamente.");
    }
    
    public function perfil()
    {
        $authUser = auth('web_usuario')->user();
        $usuario  = WebUsuario::find($authUser->id);
        return view('content.web.web-perfil', ['usuario' => $usuario]);
    }

    public function perfilUpdate(Request $request)
    {
        $authUser = auth('web_usuario')->user();
        $usuario  = WebUsuario::find($authUser->id);
     
        $request->validate([
            'email'         => 'nullable|email|max:255|unique:web_usuarios,email,' . $usuario->id,
            'telefono'      => 'nullable|string|max:255',
            'domicilio'     => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidad'     => 'nullable|string|max:255',
            'provincia'     => 'nullable|string|max:255',
            'pais'          => 'nullable|string|max:255',
            'contraseña'    => 'nullable|min:8|confirmed',
        ], [
            'email.email'           => 'El email no tiene un formato válido',
            'email.unique'          => 'Ese email ya está en uso por otra cuenta',
            'contraseña.min'        => 'La contraseña debe tener al menos 8 caracteres',
            'contraseña.confirmed'  => 'Las contraseñas no coinciden',
        ]);
     
        if ($request->filled('email')) {
            $usuario->email = $request->email;
        }
     
        $usuario->telefono      = $request->telefono;
        $usuario->domicilio     = $request->domicilio;
        $usuario->codigo_postal = $request->codigo_postal;
        $usuario->localidad     = $request->localidad;
        $usuario->provincia     = $request->provincia;
        $usuario->pais          = $request->pais;
     
        if ($request->filled('contraseña')) {
            $usuario->contraseña = \Illuminate\Support\Facades\Hash::make($request->contraseña);
        }
     
        $usuario->save();
     
        return redirect()->route('web-perfil')->with('success', 'Perfil actualizado correctamente.');
    }

    
}