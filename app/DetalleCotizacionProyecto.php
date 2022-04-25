<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleCotizacionProyecto extends Model
{
    protected $table = 'detalle_cotizacion_proyectos';

    protected $fillable = [
        'idcotizacionp',
        'idarticulo',
        'cantidad',
        'precio',
        'descuento'
    ];

    public $timestamps = false;
}
