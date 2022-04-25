<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $fillable = ['num_documento','total','forma_pago','fecha_hora','observacion','estado','idusuario'];

    public function creditable(){
        return $this->morphTo();
    }

    public function cliente(){
        return $this->belongsTo(Persona::class);
    }

    public function deposit(){
        return $this->belongsToMany(Deposit::class)->withTimestamps();
    }

}
