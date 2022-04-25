<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleIngresoJava extends Model
{
    protected $table = 'detalle_ingreso_javas';

    protected $fillable = [
        'idingresojava',
        'idjava',
        'cantidad',
        'precio_compra'
    ];

    public $timestamps = false;
}
