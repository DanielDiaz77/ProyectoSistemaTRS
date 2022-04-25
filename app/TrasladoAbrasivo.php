<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrasladoAbrasivo extends Model
{
    protected $fillable = [
        'idproveedor','idusuario','tipo_comprobante','num_comprobante',
        'fecha_hora','nueva_ubicacion','estado','impuesto','total',
        'entregado','active','observacion','file','ubicacionant'

    ];

    public function usuario(){
        return $this->belongsTo('App\User');
    }

    public function proveedor(){
        return $this->belongsTo('App\Proveedor');
    }
}
