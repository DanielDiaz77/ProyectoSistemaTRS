<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reclamo extends Model
{
    protected $fillable = [
        'idusuario','tipo_comprobante','num_comprobante',
        'fecha_hora','estado','condicion',
        'observacion','file','folio'
    ];

    public function usuario(){
        return $this->belongsTo('App\User');
    }
}
