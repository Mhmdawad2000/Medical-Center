<?php

namespace App\Http\Controllers;

use App\Models\Clinic;

use Illuminate\Http\Request;

class clinicController extends Controller
{

    public function addclinic(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $data = $request->validate(
                [
                    'clinic_num' => ['required', 'string'],
                ]
            );
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $clinics = Clinic::all();
                $bool_clinic = true;
                foreach ($clinics as $clinic) {
                    if ($clinic['clinic_num'] == $request['clinic_num']) {
                        $bool_clinic = false;
                        break;
                    }
                }
                if ($bool_clinic) {
                    $clinic = Clinic::create($data);
                    $message = [
                        'message' => 'Clinic add successfully',
                        'clinic' => $clinic
                    ];
                } else {
                    $message = [
                        'message' => 'it is already there'
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
    public function getclinicbyid(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $falid = $request->validate([
                'id' => 'required'
            ]);
            $clinic = Clinic::where('id', $request->id)->first();
            if (!$clinic) {
                return response()->json('id incorrect', 422);
            } else {
                $response = [
                    'clinic' => $clinic,
                ];
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($response);
    }
    public function getallclinics(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $clinic = Clinic::all();
            if ($clinic->isempty() || !$clinic) {
                $response = ['clinic' => 'No clinics'];
            } else {
                $response = [
                    'clinic' => $clinic,
                ];
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($response);
    }

    public function editclinic(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $data = $request->validate(
                [
                    'clinic_num' => ['required', 'string'],
                ]
            );
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $clinics = Clinic::all();
                $bool_clinic = true;
                foreach ($clinics as $clinic) {
                    if ($clinic['clinic_num'] == $request['clinic_num']) {
                        $bool_clinic = false;
                        break;
                    }
                }
                if ($bool_clinic) {
                    $clinic = Clinic::where('id', $request->id)->first();
                    $clinic->update($request->all());
                    $message = [
                        'message' => 'Clinic updated successfully',
                        'clinic' => $clinic
                    ];
                } else {
                    $message = [
                        'message' => 'it is already there'
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
}
