<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    protected $fillable = [
        'idcliente','idusuario','tipo_comprobante','num_comprobante',
        'fecha_hora','impuesto','total','forma_pago','estado','tiempo_entrega',
        'lugar_entrega','tipo_facturacion','pagado','pagado_parcial','entregado',
        'entregado_parcial','facturado','num_factura','factura_env','observacion'
    ];

    public function usuario(){
        return $this->belongsTo(User::class);
    }

    public function cliente(){
        return $this->belongsTo(Persona::class);
    }
    public function deposits(){
        return $this->morphMany(Deposit::class,'depositable');
    }
    public function ScopeUsers($query,$arrUsers){
        if($arrUsers)
            foreach($arrUsers as $user){
                $query->Orwhere([['salidas.estado','Registrado'],['salidas.idusuario',$user]]);
            }
            return $query;
    }
    public function scopeEstado($query, $estado){
        if($estado)
            return $query->where('estado',$estado);
    }

    public function scopeCriterio($query, $criterio,$buscar){
        if($buscar)
            return $query->where("salidas.$criterio",'LIKE',"%$buscar%");
    }

    public function scopeEntrega($query, $entrega) {
        if ($entrega == 'entregado') {
            return $query->where('entregado', 1);
        } elseif ($entrega == 'entrega_parcial') {
            return $query->where('entrega_parcial', 1);
        } else {
            return $query->where([['entregado',0],['entrega_parcial',0]]);
        }
    }
    public function scopePagado($query, $pagado) {
        if ($pagado == 'pagado') {
            return $query->where('pagado', 1);
        } elseif ($pagado == 'parcial') {
            return $query->where('pago_parcial', 1);
        } else {
            return $query->where([['pagado',0],['pago_parcial',0]]);
        }
    }
}
