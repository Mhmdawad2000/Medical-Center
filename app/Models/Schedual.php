<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedual extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function getdoctor_schedual_clinic()
    {

        return $this->hasMany('App\Models\Doctor_schedual_clinic');
    }
}
