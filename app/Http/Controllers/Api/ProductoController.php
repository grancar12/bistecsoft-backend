<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(): JsonResponse
    {
        $productos = Producto::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data'    => $productos,
            'message' => 'Productos obtenidos correctamente',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio'      => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $producto = Producto::create($validated);

        return response()->json([
            'data'    => $producto,
            'message' => 'Producto creado correctamente',
        ], 201);
    }

    public function show(Producto $producto): JsonResponse
    {
        return response()->json([
            'data'    => $producto,
            'message' => 'Producto obtenido correctamente',
        ]);
    }

    public function update(Request $request, Producto $producto): JsonResponse
    {
        $validated = $request->validate([
            'nombre'      => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio'      => 'sometimes|required|numeric|min:0',
            'stock'       => 'sometimes|required|integer|min:0',
        ]);

        $producto->update($validated);

        return response()->json([
            'data'    => $producto,
            'message' => 'Producto actualizado correctamente',
        ]);
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $producto->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente',
        ]);
    }
}
