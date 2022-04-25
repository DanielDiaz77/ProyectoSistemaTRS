<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['user_id','nombre','descripcion','lote',
        'cover','fecha_hora','estado','area'];

    public function links(){
        return $this->morphMany(Link::class,'linkable');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
