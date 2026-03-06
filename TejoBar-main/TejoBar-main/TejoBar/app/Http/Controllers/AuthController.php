<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm(Request $request)
    {
        // Si hay una URL de referencia, guardarla para redirigir después del login
        if ($request->has('ref')) {
            session()->put('url.intended', $request->get('ref'));
        }
        
        return view('auth.login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Buscar usuario por email
        $user = Persona::where('correo', $email)->first();

        if ($user && $user->contrasena === $password) {
            // Crear sesión personalizada
            Session::put('idPersona', $user->idPersona);
            Session::put('nombre', $user->nombre);
            Session::put('rol', $user->rol);

            // Si el usuario marcó "recordarme", extender la duración de la sesión
            if ($remember) {
                Session::put('remember_me', true);
                // Configurar la sesión para que dure 30 días
                config(['session.lifetime' => 43200]); // 30 días en minutos
            }

            // Redirigir a la URL anterior o al dashboard
            $intendedUrl = session()->pull('url.intended', route('dashboard'));
            
            // Si la URL anterior es la misma página de login, redirigir al dashboard
            if (str_contains($intendedUrl, '/login')) {
                $intendedUrl = route('dashboard');
            }
            
            return redirect($intendedUrl)->with('success', 'Bienvenido, ' . $user->nombre);
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->only('email'));
    }

    /**
     * Procesar el login y redirigir al dashboard
     */
    public function loginToDashboard(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Buscar usuario por email
        $user = Persona::where('correo', $email)->first();

        if ($user && $user->contrasena === $password) {
            // Crear sesión personalizada
            Session::put('idPersona', $user->idPersona);
            Session::put('nombre', $user->nombre);
            Session::put('rol', $user->rol);

            // Si el usuario marcó "recordarme", extender la duración de la sesión
            if ($remember) {
                Session::put('remember_me', true);
                // Configurar la sesión para que dure 30 días
                config(['session.lifetime' => 43200]); // 30 días en minutos
            }

            // Redirigir a la URL anterior o al dashboard
            $intendedUrl = session()->pull('url.intended', route('dashboard'));
            
            // Si la URL anterior es la misma página de login, redirigir al dashboard
            if (str_contains($intendedUrl, '/login')) {
                $intendedUrl = route('dashboard');
            }
            
            return redirect($intendedUrl)->with('success', 'Bienvenido, ' . $user->nombre);
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->only('email'));
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        // Limpiar todas las sesiones incluyendo "recordarme"
        Session::flush();
        
        // Limpiar también la cookie de sesión
        if (request()->hasCookie('laravel_session')) {
            cookie()->queue(cookie()->forget('laravel_session'));
        }
        
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente');
    }

    /**
     * Mostrar el formulario de registro
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Procesar el registro
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => 'required|email|unique:persona,correo|max:100',
            'contrasena' => 'required|string|min:4|max:100',
            'numero' => 'required|string|max:20',
            'rol' => 'required|in:jugador,capitan,admin'
        ]);

        $persona = Persona::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'contrasena' => $request->contrasena,
            'numero' => $request->numero,
            'rol' => $request->rol
        ]);

        // Si es jugador, crear registro en tabla jugador
        if ($request->rol === 'jugador' || $request->rol === 'capitan') {
            $persona->jugador()->create([
                'estado' => true,
                'rut' => 'RUT' . $persona->idPersona
            ]);
        }

        return redirect()->route('login')->with('success', 'Usuario registrado correctamente');
    }

    /**
     * Mostrar una lista de usuarios (solo admin)
     */
    public function index()
    {
        $usuarios = Persona::with('jugador')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Actualizar un usuario (solo admin)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => 'required|email|max:100',
            'numero' => 'required|string|max:20',
            'rol' => 'required|in:jugador,capitan,admin'
        ]);

        $usuario = Persona::findOrFail($id);
        $usuario->update($request->only(['nombre', 'correo', 'numero', 'rol']));

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Eliminar un usuario (solo admin)
     */
    public function destroy($id)
    {
        $usuario = Persona::findOrFail($id);
        
        // Eliminar registros relacionados primero
        if ($usuario->jugador) {
            // Eliminar relaciones con equipos
            $usuario->jugador->equipos()->detach();
            
            // Eliminar compras del jugador
            $usuario->jugador->compras()->delete();
            
            // Eliminar el registro de jugador
            $usuario->jugador->delete();
        }
        
        // Eliminar apartados del usuario
        $usuario->apartados()->delete();
        
        // Finalmente eliminar la persona
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}
