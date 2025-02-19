<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function getuser()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function getspecialization()
    {
        return $this->belongsTo('App\Models\Specialization', 'specialization_id');

    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');

    }
    public function getappointment()
    {

        return $this->hasMany('App\Models\Appointment');
    }
    public function getdoctor_schedual_clinic()
    {

        return $this->hasMany('App\Models\Doctor_schedual_clinic');
    }
}
