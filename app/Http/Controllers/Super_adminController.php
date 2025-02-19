<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterDoctorRequest;
use App\Http\Requests\RegisterPatientRequest;
use App\Http\Requests\RegisterSecertaryRequest;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Secertary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Super_adminController extends Controller
{
    public function registerDoctor(RegisterDoctorRequest $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $data = $request->validated();

            $user = User::create(
                [
                    'user_type' => 'doctor',
                    'birthday' => Carbon::parse($data['birthday'])->format('Y-m-d'),
                    'fname' => $request->fname,
                    'lname' => $request->lname,
                    'phone' => $request->phone,
                    'gendere' => $request->gendere,
                    'address' => $request->address,
                    'nationality' => $request->nationality,
                    'password' => $request->password,
                    'national_id' => $request->national_id,
                    'email' => $request->email,
                    'notes' => $request->notes,
                    'state' => $request->state
                ]
            );
            $id = $user->id;
            $doctor = Doctor::create(
                [
                    'user_id' => $id,
                    'hire_date' => $request->hire_date,
                    'specialization_id' => $request->specialization_id,
                    'degree' => $request->degree,
                ]
            );
            $patient = Patient::create(
                [
                    'user_id' => $id,
                ]
            );
            $message = [
                'user_table' => $user,
                'doctor' => $doctor,
                'patient' => $patient,
            ];
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($message);
    }

    public function registerSecertary(RegisterSecertaryRequest $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $data = $request->validated();
            $user = User::create(
                [
                    'user_type' => 'secertary',
                    'birthday' => Carbon::parse($data['birthday'])->format('Y-m-d'),
                    'fname' => $request->fname,
                    'lname' => $request->lname,
                    'phone' => $request->phone,
                    'gendere' => $request->gendere,
                    'address' => $request->address,
                    'nationality' => $request->nationality,
                    'password' => $request->password,
                    'national_id' => $request->national_id,
                    'email' => $request->email,
                    'notes' => $request->notes,
                    'state' => $request->state
                ]
            );
            $id = $user->id;
            $secertary = Secertary::create(
                [
                    'user_id' => $id,
                    'hire_date' => $request->hire_date,
                    'clinic_id' => $request->clinic_id
                ]
            );
            $patient = Patient::create(
                [
                    'user_id' => $id,
                ]
            );
            $message = [
                'user_table' => $user,
                'secertary' => $secertary,
                'patient' => $patient,
            ];
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($message);
    }

    public function registerAdmin(RegisterPatientRequest $request)
    {
        $data = $request->validated();
        $data['user_type'] = 'admin';
        $data['state'] = 'active';
        $data['birthday'] = Carbon::parse($data['birthday'])->format('Y-m-d');
        $user = User::create($data);
        $id = $user->id;
        $patient = Patient::create(
            [
                'user_id' => $id,
            ]
        );
        $message = [
            'user_table' => $user,
            'patient' => $patient,
        ];
        return response()->json($message);
    }
    public function makeuserbloke(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $data = $request->validate([
                'id' => ['required'],
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $user = User::where('id', $request->id)->first();
                if (!$user) {
                    $message = [
                        'message' => 'not founded id'
                    ];
                } else {
                    if ($user->user_type == 'doctor' || $user->user_type == 'secertary') {
                        $user['state'] = 'block';
                        $user->update($request->all());
                        $message = [
                            'message' => 'user updated successfully',
                            'user' => $user,
                        ];
                    } else {
                        $message = [
                            'message' => 'you dont have the role to bloke employee'
                        ];
                    }
                }
            }
        } else {
            $message = [
                'message' => 'you dont have the role'
            ];

        }
        return response()->json($message);
    }

    public function makeuseractive(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $data = $request->validate([
                'id' => ['required'],
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $user = User::where('id', $request->id)->first();
                if (!$user) {
                    $message = [
                        'message' => 'not founded id'
                    ];
                } else {
                    if ($user->user_type == 'doctor' || $user->user_type == 'secertary') {
                        $user['state'] = 'active';
                        $user->update($request->all());
                        $message = [
                            'message' => 'user updated successfully',
                            'user' => $user,
                        ];
                    } else {
                        $message = [
                            'message' => 'you don\'t have the role to active employee'
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
    public function editpatientprofile(Request $request)
    {
        $token = auth()->user()->user_type;
        $token_id = auth()->user()->id;
        if ($token == 'patient') {
            $data = $request->validate([
                'fname' => ['required', 'string'],
                'lname' => ['required', 'string'],
                'age' => ['required', 'numeric'],
                'phone' => ['required', 'numeric'],
                'gendere' => ['required', 'string'],
                'address' => ['required', 'string'],
                'nationality' => ['required', 'string'],
                'national_id' => ['required', 'numeric'],
                'notes' => ['nullable', 'string'],
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $user = User::where('id', $token_id)->first();
                $users = User::all();
                $bool = true;
                foreach ($users as $auser) {
                    if ($auser->id != $user->id && $auser->national_id == $request->national_id) {
                        $bool = false;
                    }
                }
                if ($bool) {
                    $user->update($request->all());
                    $message = [
                        'message' => 'user updated succsecfully',
                        'user' => $user
                    ];
                } else {
                    $message = ['message' => 'I\'m sorry, Plz inter inccourct national id'];
                }

            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];

        }
        return response()->json($message);
    }
}
