<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Mostrar una lista de productos
     */
    public function index()
    {
        $productos = Producto::all();
        return view('productos.index', compact('productos'));
    }

    /**
     * Mostrar el formulario para crear un nuevo producto
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Almacenar un nuevo producto
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'fechaVencimiento' => 'required|date|after_or_equal:today',
            'urlImg' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'precio.min' => 'El precio debe ser mayor a 0.',
            'fechaVencimiento.after_or_equal' => 'La fecha de vencimiento debe ser hoy o una fecha futura.',
            'stock.min' => 'El stock no puede ser negativo.'
        ]);

        $data = $request->only(['nombre', 'precio', 'stock', 'fechaVencimiento']);

        // Manejar la imagen si se sube
        if ($request->hasFile('urlImg')) {
            $image = $request->file('urlImg');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('img/productos'), $imageName);
            $data['urlImg'] = $imageName;
        }

        Producto::create($data);

        return redirect()->route('dashboard.productos')
                        ->with('success', '✅ Producto agregado con éxito.');
    }

    /**
     * Mostrar un producto específico
     */
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    /**
     * Mostrar el formulario para editar un producto
     */
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    /**
     * Actualizar un producto
     */
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'fechaVencimiento' => 'required|date|after_or_equal:today',
            'urlImg' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'precio.min' => 'El precio debe ser mayor a 0.',
            'fechaVencimiento.after_or_equal' => 'La fecha de vencimiento debe ser hoy o una fecha futura.',
            'stock.min' => 'El stock no puede ser negativo.'
        ]);

        $data = $request->only(['nombre', 'precio', 'stock', 'fechaVencimiento']);

        // Manejar la imagen si se sube
        if ($request->hasFile('urlImg')) {
            // Eliminar imagen anterior si existe
            if ($producto->urlImg && file_exists(public_path('img/productos/' . $producto->urlImg))) {
                unlink(public_path('img/productos/' . $producto->urlImg));
            }

            $image = $request->file('urlImg');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('img/productos'), $imageName);
            $data['urlImg'] = $imageName;
        }

        $producto->update($data);

        return redirect()->route('dashboard.productos')
                        ->with('success', '✅ Producto actualizado.');
    }

    /**
     * Eliminar un producto
     */
    public function destroy(Producto $producto)
    {
        // Eliminar imagen si existe
        if ($producto->urlImg && file_exists(public_path('img/productos/' . $producto->urlImg))) {
            unlink(public_path('img/productos/' . $producto->urlImg));
        }

        $producto->delete();

        return redirect()->route('dashboard.productos')
                        ->with('success', '✅ Producto eliminado.');
    }

    /**
     * Obtener productos disponibles (stock > 0)
     */
    public function disponibles()
    {
        $productos = Producto::disponibles()->get();
        return response()->json($productos);
    }

    /**
     * Obtener productos próximos a vencer
     */
    public function proximosAVencer(Request $request)
    {
        $dias = $request->input('dias', 30);
        $productos = Producto::proximosAVencer($dias)->get();
        return response()->json($productos);
    }

    /**
     * Buscar productos por nombre
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $productos = Producto::where('nombre', 'like', "%{$query}%")->get();
        return response()->json($productos);
    }
}
