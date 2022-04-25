<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleOferta extends Model
{
    protected $table = 'detalle_ofertas';

    protected $fillable = [
        'idoferta',
        'idarticulo',
        'cantidad',
        'precio',
        'descuento'
    ];

    public $timestamps = false;
}
