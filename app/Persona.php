<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = ['nombre', 'tipo_documento', 'num_documento','ciudad','domicilio',
        'telefono','email','rfc','tipo','observacion','idusuario','company','tel_company',
        'cfdi','active'];

    public function proveedor(){

        return $this->hasOne('App\Proveedor');
    }

    public function user(){

        return $this->hasOne('App\User');
    }

    public function documents(){
        return $this->morphMany(Document::class,'documentable');
    }

    public function comments(){
        return $this->morphMany(Comment::class,'commentable');
    }

    public function credits(){
        return $this->morphMany(Credit::class,'creditable');
    }

}
