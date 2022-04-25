<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleCotizacionAbrasivo extends Model
{
    protected $table = 'detalle_cotizacion_abrasivos';

    protected $fillable = [
        'idcotizacionabrasivo',
        'idabrasivo',
        'cantidad',
        'precio',
        'descuento'
    ];

    public $timestamps = false;
}
