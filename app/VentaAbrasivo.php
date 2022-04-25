<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentaAbrasivo extends Model
{
    protected $fillable = [
        'idcliente','idusuario','tipo_comprobante','num_comprobante',
        'fecha_hora','impuesto','total','forma_pago','tiempo_entrega',
        'lugar_entrega','entregado','estado','moneda','tipo_cambio',
        'observacion','num_cheque','banco','tipo_facturacion','pagado',
        'entrega_parcial','file', 'observacionpriv','facturado',
        'factura_env','pago_parcial','adeudo','num_factura','auto_entrega'
        ,'special'
    ];

    public function usuario(){
        return $this->belongsTo('App\User');
    }

    public function cliente(){
        return $this->belongsTo('App\Persona');
    }

    public function deposits(){
        return $this->morphMany(Deposit::class,'depositable');
    }

    public function projects(){
        return $this->belongsToMany('App\Project')->withTimestamps();
    }

    public function documents(){
        return $this->morphMany(Document::class,'documentable');
    }

    public function ScopeUsers($query,$arrUsers){
        if($arrUsers)
            foreach($arrUsers as $user){
                $query->Orwhere([['venta_javas.estado','Registrado'],['venta_javas.idusuario',$user]]);
            }
            return $query;
    }

    public function scopeCriterio($query, $criterio,$buscar){
        if($buscar)
            return $query->where("venta_javas.$criterio",'LIKE',"%$buscar%");
    }

    public function scopeEstado($query, $estado){
        if($estado)
            return $query->where('estado',$estado);
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
