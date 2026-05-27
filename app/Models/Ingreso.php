<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    protected $fillable = [
        'tipo',
        'descripcion',
        'monto',
        'venta_id',
        'fecha',
        'hora',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
