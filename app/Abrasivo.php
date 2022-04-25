<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Abrasivo extends Model
{
    //
    protected $table = 'abrasivos';

    protected $fillable = [
        'codigo',
        'sku',
        'precio_venta',
        'stock',
        'descripcion',
        'ubicacion',
        'file',
        'condicion',
        'comprometido',
        'idusuario'
    ];


    public function links(){
        return $this->morphMany(Link::class,'linkable');
    }
    public function user(){
        return $this->belongsTo('App\User','usuario','autoing');
    }
    public function scopeCriterio($query, $criterio,$buscar){
        if($buscar)
            return $query->where("abrasivos.$criterio",'LIKE',"%$buscar%");
    }
    public function scopeUbicacion($query, $ubicacion){
        if($ubicacion)
            return $query->where('ubicacion','LIKE',"%$ubicacion%");
    }
}
