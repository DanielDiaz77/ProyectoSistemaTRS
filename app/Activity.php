<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

    protected $fillable = ['start','end','title','content','status','idemisor','idreceptor'];

    public function users(){
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    public function comments(){
        return $this->morphMany(Comment::class,'commentable');
    }

}
