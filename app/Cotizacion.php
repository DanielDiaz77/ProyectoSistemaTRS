<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'idcliente',
        'idusuario',
        'tipo_comprobante',
        'num_comprobante',
        'vigencia',
        'fecha_hora',
        'impuesto',
        'total',
        'forma_pago',
        'tiempo_entrega',
        'lugar_entrega',
        'aceptado',
        'estado',
        'moneda',
        'tipo_cambio',
        'observacion'
    ];

    public function usuario(){
        return $this->belongsTo('App\User');
    }

    public function cliente(){
        return $this->belongsTo('App\Persona');
    }
}
