<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
    protected $fillable = [
        'title',
        'num_comprobante',
        'condicion',
        'type',
        'ubicacion'
    ];

    public function documents(){
        return $this->morphMany(Document::class,'documentable','documentable_type');
    }

    public function usuario(){
        return $this->belongsTo('App\User');
    }
}
