<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Java extends Model
{
    protected $fillable = [
        'idcategoria', 'codigo', 'sku', 'nombre',
        'terminado', 'largo', 'alto', 'metros_cuadrados',
        'espesor', 'precio_venta', 'ubicacion', 'contenedor',
        'stock', 'descripcion', 'observacion', 'origen',
        'fecha_llegada', 'file', 'condicion', 'comprometido',
        'idusuario'

    ];

    public function categoria(){
        return $this->belongsTo('App\Categoria');
    }

    public function user(){
        return $this->belongsTo('App\User','usuario','autoing');
    }

    public function scopeCriterio($query, $criterio,$buscar){
        if($buscar)
            return $query->where("javas.$criterio",'LIKE',"%$buscar%");
    }

    public function scopeTerminado($query, $terminado){
        if($terminado)
            return $query->where('terminado','LIKE',"%$terminado%");
    }

    public function scopeUbicacion($query, $ubicacion){
        if($ubicacion)
            return $query->where('ubicacion','LIKE',"%$ubicacion%");
    }

    public function scopeCategoria($query, $idcategoria){
        if($idcategoria)
            return $query->where('idcategoria','LIKE',"%$idcategoria%");
    }
}
