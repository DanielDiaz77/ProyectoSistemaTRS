<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    protected $fillable = [
        'idusuario','tipo_comprobante','num_comprobante',
        'fecha_hora','nueva_ubicacion','estado','entregado',
        'observacion','file'
    ];

    public function usuario(){
        return $this->belongsTo('App\User');
    }
}
