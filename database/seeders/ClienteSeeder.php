<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre'   => 'Carlos Ramírez',
                'email'    => 'carlos.ramirez@gmail.com',
                'telefono' => '3101234567',
            ],
            [
                'nombre'   => 'María Fernández',
                'email'    => 'maria.fernandez@hotmail.com',
                'telefono' => '3209876543',
            ],
            [
                'nombre'   => 'Andrés Torres',
                'email'    => 'andres.torres@empresa.com',
                'telefono' => '3154567890',
            ],
            [
                'nombre'   => 'Lucía Gómez',
                'email'    => 'lucia.gomez@gmail.com',
                'telefono' => '3001112233',
            ],
            [
                'nombre'   => 'Juan Pablo Herrera',
                'email'    => 'jpherrera@outlook.com',
                'telefono' => '3187654321',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
