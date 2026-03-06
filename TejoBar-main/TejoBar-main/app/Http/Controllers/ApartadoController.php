<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\Producto;
use App\Models\Historial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ApartadoController extends Controller
{
    /**
     * Mostrar una lista de apartados
     */
    public function index()
    {
        $apartados = Apartado::with(['persona', 'producto'])->get();
        return view('apartados.index', compact('apartados'));
    }

    /**
     * Mostrar apartados del usuario actual
     */
    public function misApartados()
    {
        $idPersona = Session::get('idPersona');
        
        if (!$idPersona) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        $apartados = Apartado::where('idPersona', $idPersona)
                            ->with('producto')
                            ->get();

        return view('apartados.mis-apartados', compact('apartados'));
    }

    /**
     * Apartar un producto
     */
    public function store(Request $request)
    {
        $idPersona = Session::get('idPersona');
        
        if (!$idPersona) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        $request->validate([
            'idProducto' => 'required|exists:producto,idProducto',
            'cantidad' => 'required|integer|min:1'
        ]);

        $idProducto = $request->input('idProducto');
        $cantidad = $request->input('cantidad');

        // Verificamos que el producto exista y tenga stock suficiente
        $producto = Producto::find($idProducto);

        if (!$producto) {
            return back()->with('error', '⚠️ Producto no encontrado.');
        }

        if ($producto->stock < $cantidad) {
            return back()->with('error', '⚠️ Stock insuficiente. Disponible: ' . $producto->stock);
        }

        // Creamos el apartado
        Apartado::create([
            'idPersona' => $idPersona,
            'idProducto' => $idProducto,
            'cantidad' => $cantidad,
            'estado' => 'pendiente'
        ]);

        // Lo mandamos de vuelta a la página del producto con mensaje de éxito
        return redirect()->route('productos.show', $idProducto)
                        ->with('success', '✅ Producto apartado con éxito. Puedes verlo en tu dashboard.');
    }

    /**
     * Mostrar un apartado específico
     */
    public function show(Apartado $apartado)
    {
        $apartado->load(['persona', 'producto']);
        return view('apartados.show', compact('apartado'));
    }

    /**
     * Confirmar apartado (cambiar estado a comprado)
     */
    public function confirmar(Apartado $apartado)
    {
        $apartado->update(['estado' => 'comprado']);

        // Descontamos el stock del producto
        $producto = $apartado->producto;
        $producto->decrement('stock', $apartado->cantidad);

        return back()->with('success', 'Apartado confirmado y stock actualizado');
    }

    /**
     * Entregar apartado (mover a historial)
     */
    public function entregar(Apartado $apartado)
    {
        $rol = Session::get('rol');
        
        if ($rol !== 'admin') {
            return back()->with('error', 'Solo los administradores pueden entregar apartados');
        }

        // Creamos el registro en historial
        Historial::create([
            'idPersona' => $apartado->idPersona,
            'idProducto' => $apartado->idProducto,
            'cantidad' => $apartado->cantidad,
            'precio' => $apartado->producto->precio,
            'total' => $apartado->producto->precio * $apartado->cantidad,
            'fechaEntrega' => now(),
            'estado' => 'entregado'
        ]);

        // Descontamos el stock del producto
        $producto = $apartado->producto;
        $producto->decrement('stock', $apartado->cantidad);

        // Borramos el apartado
        $apartado->delete();

        return back()->with('success', '✅ Apartado entregado y movido al historial');
    }

    /**
     * Cancelar apartado
     */
    public function cancelar(Apartado $apartado)
    {
        $apartado->delete();
        return back()->with('success', 'Apartado cancelado');
    }

    /**
     * Actualizar apartado
     */
    public function update(Request $request, Apartado $apartado)
    {
        $idPersona = Session::get('idPersona');
        
        if (!$idPersona) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        // Verificamos que el apartado pertenece al usuario actual
        if ($apartado->idPersona != $idPersona) {
            return back()->with('error', 'No tienes permisos para modificar este apartado');
        }

        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        $cantidad = $request->input('cantidad');

        // Verificamos que el producto tenga stock suficiente
        if ($apartado->producto->stock < $cantidad) {
            return back()->with('error', '⚠️ Stock insuficiente. Disponible: ' . $apartado->producto->stock);
        }

        $apartado->update([
            'cantidad' => $cantidad
        ]);

        return back()->with('success', '✅ Apartado actualizado correctamente');
    }

    /**
     * Eliminar apartado
     */
    public function destroy(Apartado $apartado)
    {
        $idPersona = Session::get('idPersona');
        
        if (!$idPersona) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        // Verificamos que el apartado pertenece al usuario actual
        if ($apartado->idPersona != $idPersona) {
            return back()->with('error', 'No tienes permisos para eliminar este apartado');
        }

        $apartado->delete();
        return back()->with('success', '✅ Apartado eliminado correctamente');
    }

    /**
     * Obtener apartados pendientes
     */
    public function pendientes()
    {
        $apartados = Apartado::pendientes()
                            ->with(['persona', 'producto'])
                            ->get();
        return response()->json($apartados);
    }

    /**
     * Obtener apartados comprados
     */
    public function comprados()
    {
        $apartados = Apartado::comprados()
                            ->with(['persona', 'producto'])
                            ->get();
        return response()->json($apartados);
    }

    /**
     * Obtener apartados por persona
     */
    public function porPersona($idPersona)
    {
        $apartados = Apartado::where('idPersona', $idPersona)
                            ->with('producto')
                            ->get();
        return response()->json($apartados);
    }

    /**
     * Obtener apartados por producto
     */
    public function porProducto($idProducto)
    {
        $apartados = Apartado::where('idProducto', $idProducto)
                            ->with('persona')
                            ->get();
        return response()->json($apartados);
    }
}
