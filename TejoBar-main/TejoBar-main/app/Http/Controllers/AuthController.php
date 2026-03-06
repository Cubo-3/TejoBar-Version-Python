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
     * Mostrar el formulario de inicio de sesión
     */
    public function showLoginForm(Request $request)
    {
        // Guardamos la URL donde estaba el usuario para llevarlo de vuelta después
        if ($request->has('ref')) {
            session()->put('url.intended', $request->get('ref'));
        }
        
        return view('auth.login');
    }

    /**
     * Procesar el inicio de sesión
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

        // Buscamos al usuario en la base de datos por su email
        $user = Persona::where('correo', $email)->first();

        if ($user && $user->contrasena === $password) {
            // Creamos la sesión del usuario con sus datos
            Session::put('idPersona', $user->idPersona);
            Session::put('nombre', $user->nombre);
            Session::put('rol', $user->rol);

            // Si marcó recordarme, hacemos que la sesión dure más tiempo
            if ($remember) {
                Session::put('remember_me', true);
                // Configurar la sesión para que dure 30 días
                config(['session.lifetime' => 43200]); // 30 días en minutos
            }

            // Lo mandamos a donde estaba antes o al dashboard
            $intendedUrl = session()->pull('url.intended', route('dashboard'));
            
            // Si estaba en login, mejor lo mandamos al dashboard
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
     * Procesar el inicio de sesión y redirigir al panel de control
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

        // Buscamos al usuario en la base de datos por su email
        $user = Persona::where('correo', $email)->first();

        if ($user && $user->contrasena === $password) {
            // Creamos la sesión del usuario con sus datos
            Session::put('idPersona', $user->idPersona);
            Session::put('nombre', $user->nombre);
            Session::put('rol', $user->rol);

            // Si marcó recordarme, hacemos que la sesión dure más tiempo
            if ($remember) {
                Session::put('remember_me', true);
                // Configurar la sesión para que dure 30 días
                config(['session.lifetime' => 43200]); // 30 días en minutos
            }

            // Lo mandamos a donde estaba antes o al dashboard
            $intendedUrl = session()->pull('url.intended', route('dashboard'));
            
            // Si estaba en login, mejor lo mandamos al dashboard
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
        // Borramos toda la información de la sesión
        Session::flush();
        
        // También borramos la cookie del navegador
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

        // Si es jugador o capitán, creamos su registro en la tabla jugador
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
        
        // Primero borramos todo lo relacionado con el usuario
        if ($usuario->jugador) {
            // Quitamos al jugador de todos los equipos
            $usuario->jugador->equipos()->detach();
            
            // Borramos todas sus compras
            $usuario->jugador->compras()->delete();
            
            // Borramos el registro del jugador
            $usuario->jugador->delete();
        }
        
        // Borramos todos sus apartados
        $usuario->apartados()->delete();
        
        // Al final borramos el usuario de la tabla persona
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}
