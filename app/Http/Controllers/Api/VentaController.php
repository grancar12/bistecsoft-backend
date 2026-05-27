<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['cliente', 'detalles.producto'])
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json(['data' => $ventas]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'              => 'required|exists:clientes,id',
            'fecha'                   => 'required|date',
            'detalles'                => 'required|array|min:1',
            'detalles.*.producto_id'  => 'required|exists:productos,id',
            'detalles.*.cantidad'     => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            $detallesData = [];

            foreach ($request->detalles as $detalle) {
                $producto = Producto::findOrFail($detalle['producto_id']);

                if ($producto->stock < $detalle['cantidad']) {
                    return response()->json([
                        'message' => "Stock insuficiente para: {$producto->nombre}"
                    ], 422);
                }

                $subtotal = $producto->precio * $detalle['cantidad'];
                $total   += $subtotal;

                $detallesData[] = [
                    'producto_id'     => $producto->id,
                    'cantidad'        => $detalle['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal'        => $subtotal,
                ];

                $producto->decrement('stock', $detalle['cantidad']);
            }

            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'fecha'      => $request->fecha,
                'total'      => $total,
            ]);

            $venta->detalles()->createMany($detallesData);

            // Registrar ingreso automático
            Ingreso::create([
                'tipo'        => 'venta',
                'descripcion' => 'Venta #' . $venta->id . ' — ' . $venta->cliente->nombre,
                'monto'       => $venta->total,
                'venta_id'    => $venta->id,
                'fecha'       => Carbon::now()->toDateString(),
                'hora'        => Carbon::now()->toTimeString(),
            ]);

            DB::commit();

            return response()->json([
                'data'    => $venta->load(['cliente', 'detalles.producto']),
                'message' => 'Venta registrada correctamente',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al registrar la venta: ' . $e->getMessage()], 500);
        }
    }

    public function show(Venta $venta)
    {
        return response()->json([
            'data' => $venta->load(['cliente', 'detalles.producto'])
        ]);
    }

    public function destroy(Venta $venta)
    {
        DB::beginTransaction();

        try {
            // Restaurar stock
            foreach ($venta->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }

            // Eliminar ingreso asociado
            Ingreso::where('venta_id', $venta->id)->delete();

            $venta->detalles()->delete();
            $venta->delete();

            DB::commit();

            return response()->json(['message' => 'Venta eliminada correctamente']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al eliminar la venta: ' . $e->getMessage()], 500);
        }
    }
}
