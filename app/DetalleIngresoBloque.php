<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleIngresoBloque extends Model
{
    protected $table = 'detalle_ingreso_bloques';

    protected $fillable = [
        'idingresobloque',
        'idbloque',
        'cantidad',
        'precio_compra'
    ];

    public $timestamps = false;
}
