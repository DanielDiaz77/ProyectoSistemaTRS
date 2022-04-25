<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleTraslado extends Model
{
    protected $table = 'detalle_traslados';

    protected $fillable = [
        'idtraslado',
        'idarticulo',
        'cantidad',
        'ubicacion'
    ];

    //public $timestamps = false;
}
