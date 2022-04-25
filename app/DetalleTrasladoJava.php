<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleTrasladoJava extends Model
{
    protected $table = 'detalle_traslado_javas';

    protected $fillable = [
        'idtrasladojava',
        'idjava',
        'cantidad',
        'ubicacion'
    ];

    public $timestamps = false;
}
