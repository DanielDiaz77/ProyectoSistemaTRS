<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVentaJava extends Model
{
    protected $table = 'detalle_venta_javas';

    protected $fillable = [
        'idventajava','idjava','cantidad','precio','descuento',
        'por_entregar','entregadas','pendientes','completado','fletero',
        'boleta','matricula'
    ];

    public $timestamps = false;

    public function venta(){
        return $this->belongsTo(VentaJava::class, 'venta_id');
    }

}
