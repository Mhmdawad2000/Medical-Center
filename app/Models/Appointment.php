<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function getdoctor()
    {
        return $this->belongsTo('App\Models\Doctor', 'doctor_id');

    }
}
