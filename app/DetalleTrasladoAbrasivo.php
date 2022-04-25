<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleTrasladoAbrasivo extends Model
{

    protected $table = 'detalle_traslado_abrasivos';

    protected $fillable = [
        'idtrasladoabrasivo',
        'idabrasivo',
        'cantidad',
        'precio_compra'
    ];

    public $timestamps = false;
}
