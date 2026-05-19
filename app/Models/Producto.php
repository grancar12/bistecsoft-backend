<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class producto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
    ];

    protected $casts = [
        'precio'     => 'decimal:2',
        'stock'      => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function detallesVenta(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    // Verifica si hay suficiente stock antes de vender
    public function tieneStock(int $cantidad): bool
    {
        return $this->stock >= $cantidad;
    }
}
