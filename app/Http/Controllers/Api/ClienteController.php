<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(): JsonResponse
    {
        $clientes = Cliente::orderBy('nombre')->get();

        return response()->json([
            'data'    => $clientes,
            'message' => 'Clientes obtenidos correctamente',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:255',
            'email'    => 'required|email|unique:clientes,email',
            'telefono' => 'nullable|string|max:55',
        ]);

        $cliente = Cliente::create($validated);

        return response()->json([
            'data'    => $cliente,
            'message' => 'Cliente creado correctamente',
        ], 201);
    }

    public function show(Cliente $cliente): JsonResponse
    {
        return response()->json([
            'data'    => $cliente->load('ventas'),
            'message' => 'Cliente obtenido correctamente',
        ]);
    }

    public function update(Request $request, Cliente $cliente): JsonResponse
    {
        $validated = $request->validate([
            'nombre'   => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|unique:clientes,email,' . $cliente->id,
            'telefono' => 'nullable|string|max:55',
        ]);

        $cliente->update($validated);

        return response()->json([
            'data'    => $cliente,
            'message' => 'Cliente actualizado correctamente',
        ]);
    }

    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado correctamente',
        ]);
    }
}
