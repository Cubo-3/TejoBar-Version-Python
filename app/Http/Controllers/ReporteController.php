<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReporteController extends Controller
{
    /**
     * Exportar reporte de productos y apartados en CSV
     */
    public function exportarProductosCSV()
    {
        // Verificación adicional de seguridad: solo administradores
        if (session('rol') !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta funcionalidad');
        }
        
        // Obtener datos
        $apartados = Apartado::with(['persona', 'producto'])->get();
        $productos = Producto::all();
        
        // Crear contenido CSV
        $csvContent = $this->generarContenidoCSV($apartados, $productos);
        
        // Configurar headers para descarga
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="reporte_productos_' . date('Y-m-d_H-i-s') . '.csv"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        
        return response($csvContent, 200, $headers);
    }
    
    /**
     * Generar contenido CSV
     */
    private function generarContenidoCSV($apartados, $productos)
    {
        $csv = '';
        
        // BOM para UTF-8 (para que Excel abra correctamente los caracteres especiales)
        $csv .= "\xEF\xBB\xBF";
        
        // Hoja 1: Apartados
        $csv .= "=== APARTADOS ===\n";
        $csv .= "Usuario,Producto,Cantidad,Precio Unitario,Total,Fecha Apartado,Estado\n";
        
        foreach ($apartados as $apartado) {
            $csv .= sprintf(
                "%s,%s,%d,%.2f,%.2f,%s,%s\n",
                $apartado->persona->nombre ?? 'Usuario',
                $apartado->producto->nombre,
                $apartado->cantidad,
                $apartado->producto->precio,
                $apartado->producto->precio * $apartado->cantidad,
                $apartado->fechaApartado->format('d/m/Y H:i'),
                $apartado->estado
            );
        }
        
        $csv .= "\n";
        
        // Hoja 2: Productos
        $csv .= "=== PRODUCTOS ===\n";
        $csv .= "ID,Nombre,Stock,Precio,Fecha Vencimiento,Imagen\n";
        
        foreach ($productos as $producto) {
            $csv .= sprintf(
                "%d,%s,%d,%.2f,%s,%s\n",
                $producto->idProducto,
                $producto->nombre,
                $producto->stock,
                $producto->precio,
                $producto->fechaVencimiento->format('d/m/Y'),
                $producto->urlImg ?? 'Sin imagen'
            );
        }
        
        $csv .= "\n";
        
        // Hoja 3: Resumen
        $csv .= "=== RESUMEN ===\n";
        $csv .= "Total Apartados," . $apartados->count() . "\n";
        $csv .= "Total Productos," . $productos->count() . "\n";
        $csv .= "Apartados Pendientes," . $apartados->where('estado', 'pendiente')->count() . "\n";
        $csv .= "Apartados Entregados," . $apartados->where('estado', 'entregado')->count() . "\n";
        $csv .= "Productos con Stock Bajo," . $productos->where('stock', '<', 10)->count() . "\n";
        
        // Calcular totales
        $totalVentas = $apartados->where('estado', 'entregado')->sum(function($apartado) {
            return $apartado->producto->precio * $apartado->cantidad;
        });
        
        $csv .= "Total Ventas,$" . number_format($totalVentas, 2) . "\n";
        
        return $csv;
    }
}
