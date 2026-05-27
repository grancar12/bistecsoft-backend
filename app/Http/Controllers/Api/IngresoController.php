<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IngresoController extends Controller
{
    // GET /api/v1/ingresos?fecha=2025-01-15
    public function index(Request $request)
    {
        $fecha = $request->get('fecha', now()->toDateString());

        $ingresos = Ingreso::with('venta.cliente')
            ->whereDate('fecha', $fecha)
            ->orderBy('hora', 'asc')
            ->get();

        $totalVentas   = $ingresos->where('tipo', 'venta')->sum('monto');
        $totalManuales = $ingresos->where('tipo', 'manual')->sum('monto');
        $totalDia      = $totalVentas + $totalManuales;

        return response()->json([
            'data'           => $ingresos,
            'total_ventas'   => $totalVentas,
            'total_manuales' => $totalManuales,
            'total_dia'      => $totalDia,
            'fecha'          => $fecha,
        ]);
    }

    // POST /api/v1/ingresos
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto'       => 'required|numeric|min:0.01',
        ]);

        $ingreso = Ingreso::create([
            'tipo'        => 'manual',
            'descripcion' => $request->descripcion,
            'monto'       => $request->monto,
            'venta_id'    => null,
            'fecha' => now()->toDateString(),
            'hora'  => now()->toTimeString(),
        ]);

        return response()->json(['data' => $ingreso], 201);
    }

    // DELETE /api/v1/ingresos/{id}
    public function destroy(Ingreso $ingreso)
    {
        if ($ingreso->tipo === 'venta') {
            return response()->json(
                ['message' => 'No se puede eliminar un ingreso de venta'],
                403
            );
        }

        $ingreso->delete();

        return response()->json(['message' => 'Ingreso eliminado correctamente']);
    }
}
