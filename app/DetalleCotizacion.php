<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    protected $table = 'detalle_cotizaciones';

    protected $fillable = [
        'idcotizacion',
        'idarticulo',
        'cantidad',
        'precio',
        'descuento'
    ];

    public $timestamps = false;
}
