<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cycles_Studying_Days extends Model
{
    
    protected $table = 'cycles_studying_days';

    public function cycles(){
        return $this->hasOne('App\Cycles','id','cycle');
    }

    public function studying_days(){
        return $this->hasOne('App\Studying_Days','id','study_day');
    }

    public function cycles_studying_days(){
        return $this->hasOne('App\Cycles_Studying_Days','id','cycle_study_day');
    }
}
