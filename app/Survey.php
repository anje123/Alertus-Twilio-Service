<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Survey extends Model
{
    protected $fillable = ['title'];
    use SoftDeletes; 
    protected $dates = ['deleted_at'];



    public function questions()
    {
        return $this->hasMany('App\Question');
    }
}
