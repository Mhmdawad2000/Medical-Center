<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;

class medicationController extends Controller
{
    //
    public function addmedication(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $data = $request->validate([
                'medication_name' => ['required', 'string']
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {

                $medications = Medication::all();
                $bool_medication = true;
                foreach ($medications as $medication) {
                    if ($medication['medication_name'] == $request['medication_name']) {
                        $bool_medication = false;
                    }
                }
                if ($bool_medication) {
                    $medication = Medication::create($data);
                    $message = [
                        'message' => 'Medication add successfully',
                        'medication' => $medication,
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

    public function getmedicationbyid(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin' || $token == 'doctor') {
            $filed = $request->validate([
                'id' => 'required',
            ]);

            $medication = Medication::where('id', $request->id)->first();
            if (!$medication) {
                return response()->json('id incorrect', 422);
            } else {
                $response = [
                    'medication' => $medication,
                ];
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($response);
    }

    public function getallmedications(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin'||$token=='doctor') {
            $medication = Medication::paginate(10);
            if ($medication->isempty() || !$medication) {
                $response = ['medication' => 'No medications'];
            } else {
                $response = [
                    'medication' => $medication,
                ];

            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($response);
    }

    public function editmedication(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $data = $request->validate(
                ['id'=>['required'],
                'medication_name' => ['required', 'string']]
            );
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $medication = Medication::where('id', $request->id)->first();
                $medication->update($request->all());
                $message = [
                    'message' => 'medication updated successfully',
                    'medication' => $medication
                ];
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($message);

    }
}
