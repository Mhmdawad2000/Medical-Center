<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Models\Specialization;

class specializationController extends Controller
{
    //
    public function addspecialization(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $data = $request->validate(['specialization_type' => ['required', 'string']]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $specializations = Specialization::all();
                $bool_specialization = true;
                foreach ($specializations as $specialization) {
                    if ($specialization['specialization_type'] == $request['specialization_type']) {
                        $bool_specialization = false;
                    }
                }
                if ($bool_specialization) {
                    $specialization = Specialization::create($data);
                    $message = [
                        'message' => 'specialization add successfully',
                        'specialization' => $specialization,
                    ];
                } else {
                    $message = [
                        'message' => 'it is already there'
                    ];
                }
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role',
            ];
        }
        return response()->json($message);

    }
    public function editspecialization(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $data = $request->validate([
                'id' => ['required'],
                'specialization_type' => ['required', 'string']]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $bool = false;
                $specializations = Specialization::all();
                foreach ($specializations as $specialization) {
                    if ($specialization->specialization_type == $request->specialization_type) {
                        $bool = true;
                    }
                }
                if ($bool) {
                    $message = [
                        'message' => 'it is already there'
                    ];
                } else {
                    $specialization = Specialization::where('id', $request->id)->first();
                    $specialization->update($request->all());
                    $message = [
                        'message' => 'specialization updated successfully',
                        'specialization_table' => $specialization,
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
    public function getallspecializations(Request $request)
    {
        $specialization = Specialization::all();
        if ($specialization->isempty() || !$specialization) {
            $response = ['specialization' => 'No specializations'];
        } else {
            $response = [
                'specialization' => $specialization,
            ];
            return response()->json($response);
        }
    }

}
