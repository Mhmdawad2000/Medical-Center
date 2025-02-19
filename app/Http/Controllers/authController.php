<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterPatientRequest;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Secertary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class authController extends Controller
{
    public function login(Request $request)
    {

        $filed = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json('email incorrect', 422);
        } else {
            if (!Hash::check($request->password, $user->password)) {
                return response()->json('password incorrect', 422);
            } else {
                if ($user->state == 'active') {
                    $user_type = $user->user_type;
                    $user_id = $user->id;
                    $patient = Patient::where('user_id', $user_id)->first();
                    $token = $user->createToken('auth_token')->plainTextToken;
                    $user['remember_token'] = $token;
                    $user->update();
                    $messages = Message::where('user_id', $user->id)->latest()->get();
                    $MYMessage = null;
                    foreach ($messages as $message) {
                        $MYMessage[] = $message;
                    }
                        if ($user_type == 'doctor') {
                            $doctor = Doctor::where('user_id', $user_id)->first();
                            $response = [
                                'message' => 'logged in successfully',
                                'token' => $token,
                                'user_type' => $user_type,
                                'id' => $user_id,
                                'doctor_id' => $doctor->id,
                                'patient_id' => $patient->id,
                                'MYMessage' => $MYMessage
                            ];
                        } elseif ($user_type == 'secertary') {
                            $secertary = Secertary::where('user_id', $user_id)->first();
                            $response = [
                                'message' => 'logged in successfully',
                                'token' => $token,
                                'user_type' => $user_type,
                                'id' => $user_id,
                                'secertary_id' => $secertary->id,
                                'patient_id' => $patient->id,
                                'MYMessage' => $MYMessage
                            ];
                        } else {
                            $response = [
                                'message' => 'logged in successfully',
                                'token' => $token,
                                'user_type' => $user_type,
                                'id' => $user_id,
                                'patient_id' => $patient->id,
                                'MYMessage' => $MYMessage
                            ];
                        }
                } elseif ($user->state == 'pending') {
                    $user_type = $user->user_type;
                    $user_id = $user->id;
                    $patient = Patient::where('user_id', $user_id)->first();
                    $create_date_after_week = Carbon::parse($user->created_at)->addWeek()->format('Y-m-d H:i:s');
                    $now_date = Carbon::now()->format('Y-m-d H:i:s');
                    if ($now_date >= $create_date_after_week) {
                        $response = [
                            'message' => 'your acount state has been blocked please talk with receptionist'
                        ];
                        $user['state']='block';
                        $user->update();
                    } else {
                        $token = $user->createToken('auth_token')->plainTextToken;
                        $user['remember_token'] = $token;
                        $user->update();
                        $messages = Message::where('user_id', $user->id)->latest()->get();
                        $MYMessage = null;
                        foreach ($messages as $message) {
                            $MYMessage[] = $message;
                        }
                        $response = [
                                'message' => 'logged in successfully',
                                'token' => $token,
                                'user_type' => $user_type,
                                'id' => $user_id,
                                'patient_id' => $patient->id,
                                'MYMessage' => $MYMessage
                            ];
                    }
                } else {
                    $response = [
                        'message' => 'your account is blocked please talk with the receptionist to active your account'
                    ];
                }
            }
        }
        return response()->json($response);
    }

    public function register_patient(RegisterPatientRequest $request)
    {
        $data = $request->validated();
        $data['birthday']=Carbon::parse($data['birthday'])->format('Y-m-d');
        $user = User::create($data);
        $id = $user->id;
        $patient = Patient::create(
            [
                'user_id' => $id,
            ]
        );
        $message = [
            'user_type' => $user,
            'patient' => $patient,
        ];
        return response()->json($message);
    }


}
