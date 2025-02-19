<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor_schedual_clinic extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function getdoctor()
    {
        return $this->belongsTo('App\Models\Doctor', 'doctor_id');

    }
    public function getclinic()
    {
        return $this->belongsTo('App\Models\Clinic', 'clinic_id');

    }
    public function getschedual()
    {
        return $this->belongsTo('App\Models\schedual', 'schedual_id');

    }

}
