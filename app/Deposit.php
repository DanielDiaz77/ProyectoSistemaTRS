<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = ['total','fecha_hora','forma_pago'];

    public function depositable(){
        return $this->morphTo();
    }

    public function venta(){
        return $this->belongsTo(Venta::class);
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function ventas(){
        return $this->belongsTo(VentaAbrasivo::class);
    }

    public function credits(){
        return $this->belongsToMany(Credit::class)->withTimestamps();
    }

}
