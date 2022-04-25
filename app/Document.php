<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = ['url','tipo'];

    public function documentable(){
        return $this->morphTo();
    }

    public function project(){
        return $this->belongsTo('App\Project');
    }


}
