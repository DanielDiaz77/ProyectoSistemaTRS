<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVentaAbrasivo extends Model
{
    protected $table = 'detalle_venta_abrasivos';

    protected $fillable = [
        'idventaabrasivo','idabrasivo','cantidad','precio','descuento',
        'por_entregar','entregadas','pendientes','completado'
    ];

    public $timestamps = false;

    public function venta(){
        return $this->belongsTo(VentaAbrasivo::class, 'venta_id');
    }

    public function fletero(){
        return $this->morphMany(Fletero::class,'idventa','boleta');
    }
}
