<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Question extends Model
{
    protected $fillable = ['body', 'kind' , 'survey_id'];

    use SoftDeletes; 
    protected $dates = ['deleted_at'];

    public function survey()
    {
        return $this->belongsTo('App\Survey', 'survey_id');
    }

    public function responses()
    {
        return $this->hasMany('App\QuestionResponse');
    }
}
