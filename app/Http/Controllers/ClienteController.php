<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Admin: clientes del "Área clientes". No hay registro público: se crean acá
 * y ven todos los productos activos del Área clientes en "Mis productos".
 */
class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->q;
                $query->where(fn ($sub) => $sub
                    ->where('nombre', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%"));
            })
            ->orderBy('nombre')
            ->paginate(50)
            ->withQueryString();

        return view('content.admin.clientes', compact('clientes'));
    }

    public function create()
    {
        return view('content.admin.clientes-creador', [
            'cliente' => new Cliente(['active' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        Cliente::create([
            ...$datos,
            'usuario' => $datos['email'], // inicia sesión con el mail
            'contraseña' => Hash::make($request->input('contraseña')),
            'active' => true,
        ]);

        return redirect()->route('adm.clientes')->with('success', 'Cliente creado correctamente.');
    }

    public function show($id)
    {
        return view('content.admin.clientes-editor', [
            'cliente' => Cliente::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        $datos = $this->validar($request, $cliente);

        if ($cliente->usuario === $cliente->email) {
            $datos['usuario'] = $datos['email'];
        }
        $cliente->fill([...$datos, 'active' => $request->boolean('active')]);

        if ($request->filled('contraseña')) {
            $cliente->contraseña = Hash::make($request->input('contraseña'));
        }

        $cliente->save();

        return redirect()->route('adm.clientes')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy($id)
    {
        Cliente::findOrFail($id)->delete();

        return redirect()->route('adm.clientes')->with('success', 'Cliente eliminado.');
    }

    public function switch($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->active = !$cliente->active;
        $cliente->save();

        return back();
    }

    /** Valida y devuelve nombre y email (la contraseña se maneja aparte). */
    private function validar(Request $request, ?Cliente $cliente = null): array
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255',
                Rule::unique('clientes', 'email')->ignore($cliente?->id),
                Rule::unique('clientes', 'usuario')->ignore($cliente?->id)],
            'contraseña' => [$cliente ? 'nullable' : 'required', 'string', 'min:6', 'confirmed'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.required' => 'El mail es obligatorio.',
            'email.email' => 'El mail no es válido.',
            'email.unique' => 'Ya hay un cliente con ese mail.',
            'contraseña.required' => 'La contraseña es obligatoria.',
            'contraseña.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'contraseña.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        return $request->only(['nombre', 'email']);
    }

}
