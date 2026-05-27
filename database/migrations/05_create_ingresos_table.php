<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['venta', 'manual']);
            $table->string('descripcion');
            $table->decimal('monto', 10, 2);
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
 
