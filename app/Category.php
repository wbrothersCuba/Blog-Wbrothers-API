<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    
    //one to many
    public function posts(){
        return $this->hasMany('App\Post');
    }
    
    
    //one to many
    public function users(){
        return $this->hasMany('App\User');
    }
}
