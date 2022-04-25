<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleSalida extends Model
{
    protected $table = 'detalle_salidas';

    protected $fillable = [
        'idsalida','idarticulo','cantidad','precio','descuento',
    ];

    public $timestamps = false;

    public function salida(){
        return $this->belongsTo(Salida::class, 'salida_id');
    }
}
