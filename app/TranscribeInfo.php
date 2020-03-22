<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranscribeInfo extends Model
{
    protected $fillable = ['start_time', 'end_time', 'recording_sid','status'];

}
