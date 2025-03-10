<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
   protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function password(): Attribute
    {
        return new Attribute(

            set: fn ($value) => Hash::make($value),
        );
    }

    public function getPatient()
    {

        return $this->hasOne('App\Models\Patient');
    }
    public function getsecertary()
    {

        return $this->gethasOne('App\Models\Secertary');
    }
    public function getdoctor()
    {

        return $this->hasOne('App\Models\Doctor');
    }
    public function getappointment()
    {
        return $this->hasMany('App\Models\Appointment');
    }
}
