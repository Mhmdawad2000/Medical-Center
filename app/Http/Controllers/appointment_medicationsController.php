<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Appointment_medication;
use App\Models\Doctor;
use App\Models\Medication;
use Illuminate\Http\Request;

class appointment_medicationsController extends Controller
{
    public function addappointment_medication(Request $request)
    {

        $data = $request->validate([
            'appointment_id' => ['required'],
            'medication_id' => ['required']
        ]);
        if (!$data) {
            $message = [
                'message' => 'inccorect requireds'
            ];
        } else {
            $appointment = Appointment::where('id', $request->appointment_id)->first();
            $token = auth()->user()->user_type;
            $token_id = auth()->user()->id;
            $doctor = Doctor::where('user_id', $token_id)->first();
            if ($token == 'doctor' && $doctor->id == $appointment->doctor_id) {
                $already = false;
                $appoint_medicas = Appointment_medication::where('appointment_id', $request->appointment_id)->get();
                foreach ($appoint_medicas as $appoint_medica) {
                    if ($appoint_medica->medication_id == $request->medication_id) {
                        $already = true;
                        break;
                    }
                }
                if ($already) {
                    $message = [
                        'message' => 'it is already there'
                    ];
                } else {
                    $appointment_medication = Appointment_medication::create($data);
                    $message = [
                        'message' => 'Appointment_medication add successfully',
                        'Appointment_medication' => $appointment_medication,
                    ];
                }
            } else {
                $message = [
                    'message' => 'you don\'t have the role to create that user'
                ];
            }
        }
        return response()->json($message);

    }
    public function getappointment_medicationbyid(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'doctor') {

            $filed = $request->validate([
                'id' => 'required',
            ]);

            $Appointment_medication = Appointment_medication::where('id', $request->id)->first();
            if (!$Appointment_medication) {
                return response()->json('id incorrect', 422);
            } else {
                $response = [
                    'Appointment_medication' => $Appointment_medication,
                ];
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role to create that user'
            ];
        }
        return response()->json($response);
    }
    public function editappointment_medication(Request $request)
    {
        $data = $request->validate([
            'appointment_id' => ['required'],
            'medication_id' => ['required'],
            'id' => ['required']
        ]);
        if (!$data) {
            $message = [
                'message' => 'inccorect requireds'
            ];
        } else {
            $appointment = Appointment::where('id', $request->appointment_id)->first();
            $token = auth()->user()->user_type;
            $token_id = auth()->user()->id;
            $doctor = Doctor::where('user_id', $token_id)->first();
            if ($token == 'doctor' && $doctor->id == $appointment->doctor_id) {
                $appointment_medication = Appointment_medication::where('id', $request->id)->first();
                $appointment_medication->update($request->all());
                $message = [
                    'message' => 'appointment medication updated successfully',
                    'Appointment_medication_table' => $appointment_medication,
                ];
            } else {
                $message = [
                    'message' => 'you don\'t have the role to create that user'
                ];
            }
        }
        return response()->json($message);
    }
    public function getallAppointment_medications(Request $request)
    {
        $token = auth()->user()->user_type;
        $token_id = auth()->user()->id;
        $doctor = Doctor::where()->first;
        if ($token == 'doctor') {

            $date = $request->validate([
                'appointment_id' => ['required']
            ]);
            $appointment_medications = Appointment_medication::where('appointment_id', $request->appointment_id)->get();
            if ($appointment_medications->isempty() || !$appointment_medications) {
                $response = ['Appointment_medication' => 'No Appointment_medications'];
            } else {
                $table = null;
                foreach ($appointment_medications as $appointment_medication) {
                    $medication = Medication::where('id', $appointment_medication->medication_id)->first();
                    $table[] = $medication->name;
                }
                $response = [
                    'Appointment' => $table,
                ];

            }
        } else {
            $message = [
                'message' => 'you don\'t have the role to create that user'
            ];
        }
        return response()->json($response);
    }
}
