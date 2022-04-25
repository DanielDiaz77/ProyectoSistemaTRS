<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleIngresoAbrasivo extends Model
{
    protected $table = 'detalle_ingreso_abrasivos';

    protected $fillable = [
        'idingresoabrasivo',
        'idabrasivo',
        'cantidad',
        'precio_compra'
    ];

    public $timestamps = false;
}
