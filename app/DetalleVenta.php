<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';

    protected $fillable = [
        'idventa','idarticulo','cantidad','precio','descuento',
        'por_entregar','entregadas','pendientes','completado','fletero',
        'boleta','matricula'
    ];

    public $timestamps = false;

    public function venta(){
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function fletero(){
        return $this->morphMany(Fletero::class,'idventa','boleta');
    }
}
