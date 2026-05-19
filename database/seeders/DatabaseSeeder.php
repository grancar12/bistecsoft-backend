<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
        {
            $this->call([
                ClienteSeeder::class,   // 1ro — sin dependencias
                ProductoSeeder::class,  // 2do — sin dependencias
                VentaSeeder::class,     // 3ro — depende de clientes y productos
            ]);
        }


}
