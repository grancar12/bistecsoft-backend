<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index(): JsonResponse
    {
        $ventas = Venta::with(['cliente', 'detalles.producto'])
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json([
            'data'    => $ventas,
            'message' => 'Ventas obtenidas correctamente',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id'                    => 'required|exists:clientes,id',
            'fecha'                         => 'required|date',
            'detalles'                      => 'required|array|min:1',
            'detalles.*.producto_id'        => 'required|exists:productos,id',
            'detalles.*.cantidad'           => 'required|integer|min:1',
            'detalles.*.precio_unitario'    => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Crear la venta con total en 0 (se recalcula al final)
            $venta = Venta::create([
                'cliente_id' => $validated['cliente_id'],
                'fecha'      => $validated['fecha'],
                'total'      => 0,
            ]);

            foreach ($validated['detalles'] as $detalle) {
                $producto = Producto::findOrFail($detalle['producto_id']);

                // Validar stock disponible
                if (!$producto->tieneStock($detalle['cantidad'])) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Stock insuficiente para el producto: {$producto->nombre}",
                    ], 422);
                }

                // Crear detalle (subtotal se calcula automáticamente en el model)
                $venta->detalles()->create([
                    'producto_id'     => $detalle['producto_id'],
                    'cantidad'        => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal'        => 0, // Se sobreescribe en el evento saving
                ]);

                // Descontar stock
                $producto->decrement('stock', $detalle['cantidad']);
            }

            // Recalcular total de la venta
            $venta->recalcularTotal();

            DB::commit();

            return response()->json([
                'data'    => $venta->load(['cliente', 'detalles.producto']),
                'message' => 'Venta registrada correctamente',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar la venta',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Venta $venta): JsonResponse
    {
        return response()->json([
            'data'    => $venta->load(['cliente', 'detalles.producto']),
            'message' => 'Venta obtenida correctamente',
        ]);
    }

    public function destroy(Venta $venta): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Restaurar stock de cada producto al eliminar la venta
            foreach ($venta->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }

            $venta->detalles()->delete();
            $venta->delete();

            DB::commit();

            return response()->json([
                'message' => 'Venta eliminada correctamente',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar la venta',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
