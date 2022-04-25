<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'idcliente','idusuario','num_comprobante','title','content','inicio',
        'fin','total','adeudo','forma_pago','lugar_entrega','estado','pagado',
        'pagado_parcial','entregado','entregado_parcial','flete','instalacion',
        'area','tipo_facturacion','observacion','observacionpriv','impuesto',
        'tipo_comprobante'
    ];

    public function ventas(){
        return $this->belongsToMany('App\Venta')->withTimestamps();
    }

    public function usuario(){
        return $this->belongsTo('App\User');
    }

    public function cliente(){
        return $this->belongsTo('App\Persona');
    }

    public function deposits(){
        return $this->morphMany(Deposit::class,'depositable');
    }

    public function documents(){
        return $this->morphMany(Document::class,'documentable','documentable_type');
    }

    public function ScopeUsers($query,$arrUsers){
        if($arrUsers)
            foreach($arrUsers as $user){
                $query->Orwhere([['projects.estado','Registrado'],['projects.idusuario',$user]]);
            }
            return $query;
    }

}
