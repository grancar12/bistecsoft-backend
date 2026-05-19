<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre'      => 'Almuerzo',
                'descripcion' => 'almuerzo del dia',
                'precio'      => 8000,
                'stock'       => 50,
            ],
            [
                'nombre'      => 'desayuno',
                'descripcion' => 'desayuno del dia',
                'precio'      => 8000,
                'stock'       => 40,
            ],
            [
                'nombre'      => 'proteina',
                'descripcion' => 'proteina adicional',
                'precio'      => 5000,
                'stock'       => 10,
            ],
            [
                'nombre'      => 'sopa',
                'descripcion' => 'sopa adicional',
                'precio'      => 5000,
                'stock'       => 20,
            ],
            [
                'nombre'      => 'hicopor',
                'descripcion' => 'hicopor adicional',
                'precio'      => 1000,
                'stock'       => 7,
            ],
            /*[
                'nombre'      => 'Almuerzo',
                'descripcion' => 'cerdo con frijol',
                'precio'      => 8000,
                'stock'       => 20,
            ],
            [
                'nombre'      => 'Almuerzo',
                'descripcion' => 'cerdo con frijol',
                'precio'      => 8000,
                'stock'       => 18,
            ],
            [
                'nombre'      => 'Almuerzo',
                'descripcion' => 'cerdo con frijol',
                'precio'      => 8000,
                'stock'       => 8,
            ],*/
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
