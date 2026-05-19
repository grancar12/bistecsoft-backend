<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class VentaSeeder extends Seeder
{
    public function run(): void
    {
        $ventas = [
            // Venta 1 — Carlos Ramírez compra laptop + mouse
            [
                'cliente_id' => 1,
                'fecha'      => '2025-01-10',
                'detalles'   => [
                    ['producto_id' => 1, 'cantidad' => 1, 'precio_unitario' => 8000],
                    ['producto_id' => 4, 'cantidad' => 1, 'precio_unitario' => 8000],
                ],
            ],
            // Venta 2 — María Fernández compra monitor + teclado + webcam
            [
                'cliente_id' => 2,
                'fecha'      => '2025-01-15',
                'detalles'   => [
                    ['producto_id' => 2, 'cantidad' => 1, 'precio_unitario' => 8000],
                    ['producto_id' => 3, 'cantidad' => 1, 'precio_unitario' => 8000],
                    ['producto_id' => 5, 'cantidad' => 1, 'precio_unitario' => 1000],
                ],
            ],
            // Venta 3 — Andrés Torres compra auriculares + disco duro
            [
                'cliente_id' => 3,
                'fecha'      => '2025-02-03',
                'detalles'   => [
                    ['producto_id' => 1, 'cantidad' => 1, 'precio_unitario' => 8000],
                    ['producto_id' => 5, 'cantidad' => 2, 'precio_unitario' => 1000],
                ],
            ],
            // Venta 4 — Lucía Gómez compra silla gamer
            [
                'cliente_id' => 4,
                'fecha'      => '2025-02-20',
                'detalles'   => [
                    ['producto_id' => 4, 'cantidad' => 1, 'precio_unitario' => 8000],
                ],
            ],
            // Venta 5 — Juan Pablo compra teclado + mouse + webcam
            [
                'cliente_id' => 5,
                'fecha'      => '2025-03-05',
                'detalles'   => [
                    ['producto_id' => 3, 'cantidad' => 2, 'precio_unitario' => 8000],
                    ['producto_id' => 4, 'cantidad' => 1, 'precio_unitario' => 8000],
                    ['producto_id' => 5, 'cantidad' => 1, 'precio_unitario' => 1000],
                ],
            ],
        ];

        foreach ($ventas as $ventaData) {
            DB::beginTransaction();

            try {
                $venta = Venta::create([
                    'cliente_id' => $ventaData['cliente_id'],
                    'fecha'      => $ventaData['fecha'],
                    'total'      => 0,
                ]);

                foreach ($ventaData['detalles'] as $detalle) {
                    $venta->detalles()->create([
                        'producto_id'     => $detalle['producto_id'],
                        'cantidad'        => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal'        => 0, // Se sobreescribe por el evento saving del model
                    ]);

                    // Descontar stock
                    Producto::find($detalle['producto_id'])
                        ->decrement('stock', $detalle['cantidad']);
                }

                // Recalcular total con los subtotales reales
                $venta->recalcularTotal();

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("Error en venta cliente_id {$ventaData['cliente_id']}: {$e->getMessage()}");
            }
        }

        $this->command->info('✓ Ventas y detalles creados correctamente.');
    }
}
