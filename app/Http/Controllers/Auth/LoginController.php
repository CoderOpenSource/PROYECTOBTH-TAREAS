<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if ($usuario && Hash::check($request->password, $usuario->contraseña)) {
            $request->session()->put('usuario_id', $usuario->id);
            $request->session()->put('rol', $usuario->rol);
            $request->session()->put('nombre', $usuario->nombre);

            // Si el usuario es estudiante, guardar su curso_id en la sesión
            if ($usuario->rol === 'estudiante') {
                $request->session()->put('curso_id', $usuario->curso_id);
                return redirect('/dashboard/');
            }

            return redirect('/dashboard');
        }

        return back()->withErrors([
            'correo' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/');
    }
}
