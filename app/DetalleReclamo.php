<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleReclamo extends Model
{
    protected $table = 'detalle_reclamos';

    protected $fillable = [
        'idreclamo',
        'idarticulo',
        'cantidad',
    ];
}
