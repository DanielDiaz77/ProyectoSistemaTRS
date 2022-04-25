<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleCotizacionJava extends Model
{
    protected $table = 'detalle_cotizacion_javas';

    protected $fillable = [
        'idcotizacionjava',
        'idjava',
        'cantidad',
        'precio',
        'descuento'
    ];

    public $timestamps = false;
}
