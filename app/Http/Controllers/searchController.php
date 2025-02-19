<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Secertary;
use App\Models\Specialization;
use App\Models\User;
use App\Models\Secertary_schedual;
use App\Models\Schedual;
use Illuminate\Http\Request;

class searchController extends Controller
{
    //
    public function getdoctorsbyspecialization(Request $request)
    {
        $token = auth()->user()->user_type;
        $token_id = auth()->user()->id;
        $specialization = Specialization::where('specialization_type', 'LIKE', '%' . $request->specialization_type . '%')->first();
        if (!$specialization) {
            $message = [
                'Doctors' => 'not founded Specialization'
            ];
        } else {
            $doctors = Doctor::where('specialization_id', $specialization->id)->with('getspecialization')->with('getuser')->paginate(10);
            if ($doctors->isempty()) {
                $message = [
                    'Doctors' => 'not founded Doctors'
                ];
            } else {
                $response = null;

                foreach ($doctors as $doctor) {
                    if ($token != 'doctor' || $token == 'doctor' && $doctor->user_id == $token_id) {
                        $response[] = [

                            'id' => $doctor->id,
                            'user_id' => $doctor->getuser->id,
                            'fname' => $doctor->getuser->fname,
                            'lname' => $doctor->getuser->lname,
                            'hire_date' => $doctor->hire_date,
                            'specialization_type' => $doctor->getspecialization->specialization_type,
                            'degree' => $doctor->degree,
                            'geneder' => $doctor->getuser->gendere,
                            'birthday' => $doctor->getuser->birthday,
                            'address' => $doctor->getuser->address,
                            'phone' => $doctor->getuser->phone,

                        ];
                    }
                }
                $message = [
                    'Doctors' => $response
                ];
            }
        }
        return response()->json($message);
    }


    public function getdoctorsbyname(Request $request)
    {
        $token=auth()->user()->user_type;
        $token_id=auth()->user()->id;
        $users = User::where('fname', 'LIKE', $request->name . '%')->orwhere('lname', 'LIKE', $request->name . '%')->get();
        if (!$users) {
            $message = [
                'Doctors' => 'not founded name'
            ];
        } else {
            if ($users->isempty()) {
                $message = [
                    'Doctors' => 'not founded Doctors'
                ];
            } else {
                $doctors = null;
                foreach ($users as $user) {
                    if ($user->user_type == 'doctor') {
                        if($token != 'doctor'||$token == 'doctor'&&$token_id!=$user->id){
                        $doctors[] = Doctor::where('user_id', $user->id)->first();
                    }}
                }

                if ($doctors == null) {
                    $message = [
                        'Doctors' => 'not founded Doctors'
                    ];
                } else {
                    foreach ($doctors as $doctor) {
                        $user = User::where('id', $doctor->user_id)->first();
                        $specialization = Specialization::where('id', $doctor->specialization_id)->first();
                        $response[] = [

                            'id' => $doctor->id,
                            'user_id' => $user->id,
                            'state' => $user->state,
                            'fname' => $user->fname,
                            'lname' => $user->lname,
                            'hire_date' => $doctor->hire_date,
                            'specialization_type' => $specialization->specialization_type,
                            'degree' => $doctor->degree,
                            'geneder' => $user->gendere,
                            'birthday' => $user->birthday,
                            'address' => $user->address,
                            'phone' => $user->phone,

                        ];

                    }
                    $message = [
                        'Doctors' => $response
                    ];
                }


            }
            return response()->json($message);
            ;
        }

    }

    public function getmedicationbyname(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin' || $token == 'doctor') {
            $medications = Medication::where('medication_name', 'LIKE', $request->name . '%')->get();
            if (!$medications) {
                $message = [
                    'medications' => 'not founded name'
                ];
            } else {

                if ($medications->isempty()) {
                    $message = [
                        'medications' => 'not founded medications'
                    ];
                } else {
                    $message = [
                        'medications' => $medications
                    ];
                }

            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($message);
    }
    public function getclinicbynum(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $clinics = Clinic::where('clinic_num', 'LIKE', $request->name . '%')->get();
            if (!$clinics) {
                $message = [
                    'clinics' => 'not founded number'
                ];
            } else {

                if ($clinics->isempty()) {
                    $message = [
                        'clinics' => 'not founded clinics'
                    ];
                } else {
                    $message = [
                        'clinics' => $clinics
                    ];
                }

            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($message);
    }
    public function getsecertarybyname(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $users = User::where('fname', 'LIKE', $request->name . '%')->orwhere('lname', 'LIKE', $request->name . '%')->get();
            if (!$users) {
                $message = [
                    'secertaries' => 'not founded name'
                ];
            } else {

                if ($users->isempty()) {
                    $message = [
                        'secertaries' => 'not founded Secertarys'
                    ];
                } else {
                    $secertaries = null;
                    foreach ($users as $user) {
                        if ($user->user_type == 'secertary') {
                            $secertaries[] = Secertary::where('user_id', $user->id)->first();
                        }
                    }

                    if ($secertaries == null) {
                        $message = [
                            'secertaries' => 'not founded Secertarys'
                        ];
                    } else {
                        foreach ($secertaries as $secertary) {
                            $user = User::where('id', $secertary->user_id)->first();
                            $clinic = Clinic::where('id', $secertary->clinic_id)->first();
                            $response[] = [

                                'id' => $secertary->id,
                                'user_id' => $user->id,
                                'fname' => $user->fname,
                                'lname' => $user->lname,
                                'state' => $user->state,
                                'hire_date' => $secertary->hire_date,
                                'clinic_num' => $clinic->clinic_num,
                                'geneder' => $user->gendere,
                                'birthday' => $user->birthday,
                                'address' => $user->address,
                                'phone' => $user->phone,

                            ];

                        }
                        $message = [
                            'secertaries' => $response
                        ];
                    }

                }

            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($message);

    }

    public function getpatientsbyname(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'doctor' || $token == 'secertary') {
            $users = User::where('fname', 'LIKE', $request->name . '%')->orwhere('lname', 'LIKE', $request->name . '%')->get();
            if (!$users) {
                $message = [
                    'patients' => 'not founded name'
                ];
            } else {

                if ($users->isempty()) {
                    $message = [
                        'patients' => 'not founded patients'
                    ];
                } else {
                    $patients = null;
                    foreach ($users as $user) {
                        if ($user->user_type == 'patient') {
                            $patient=Patient::where('user_id', $user->id)->first();
                            $patients[] =[

                                'id' => $patient->id,
                                'user_id' => $user->id,
                                'fname' => $user->fname,
                                'lname' => $user->lname,
                                'state' => $user->state,
                                'geneder' => $user->gendere,
                                'birthday' => $user->birthday,
                                'address' => $user->address,
                                'phone' => $user->phone,
                                'national_id' => $user->national_id,

                            ];
                        }
                    }

                    if ($patients == null) {
                        $message = [
                            'patients' => 'not founded patients'
                        ];
                    } else {

                        $message = [
                            'patients' => $patients
                        ];
                    }
                }

            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($message);
    }
    public function getsecertary_schedual(Request $request)
    {
        $data = $request->validate([
            'secertary_id' => ['required'],
        ]);
        $token = auth()->user()->user_type;
        $token_id = auth()->user()->id;
        if ($token == 'admin' || ($token == 'secertary' && $token_id == $request->secertary_id && !$data)) {
            if (!$data) {
                $response = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $secertary_scheduals = Secertary_schedual::where('secertary_id', $request->secertary_id)->get();
                if ($secertary_scheduals->isempty()) {
                    $response = ['secertary_schedual' => 'No secertary scheduals'];
                } else {
                    foreach ($secertary_scheduals as $secertary_schedual) {
                        $secertary_sched[] = Schedual::where('id', $secertary_schedual->id)->first();
                    }
                    $response = [
                        'secertary_schedual' => $secertary_sched
                    ];
                }
            }
        } else {
            $response = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($response);
    }

    public function getusersbyname(Request $request)
    {
        $users = User::where('fname', 'LIKE', $request->name . '%')->orwhere('lname', 'LIKE', $request->name . '%')->get();
        if (!$users) {
            $message = [
                'users' => 'not founded name'
            ];
        } else {

            if ($users->isempty()) {
                $message = [
                    'users' => 'not founded users'
                ];
            } else {
                $message = [
                    'users' => $users
                ];
            }
            return response()->json($message);
            ;
        }
    }
    public function getallappointments(Request $request)
    {
        $data = $request->validate([
            'state' => ['required', 'string']
        ]);
        $appointments = Appointment::where('state', $request->state)->get();
        if (!$appointments) {
            $message = [
                'appointments' => 'not founded state'
            ];
        } else {

            if ($appointments->isempty()) {
                $message = [
                    'appointments' => 'not founded appointments'
                ];
            } else {
                $message = [
                    'users' => $appointments
                ];
            }
            return response()->json($message);
            ;
        }
    }



}
