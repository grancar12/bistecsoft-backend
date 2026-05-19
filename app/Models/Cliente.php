<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class cliente extends Model
{
    protected $fillable = [
        'nombre',
        'email',
        'telefono',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }
}
