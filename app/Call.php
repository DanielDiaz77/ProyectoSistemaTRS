<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $table = 'calls';

    protected $fillable = ['idcliente','idusuario','body','status','area'];

    public function usuario(){
        return $this->belongsTo('App\User');
    }

    public function comments(){
        return $this->morphMany(Comment::class,'commentable');
    }

}
