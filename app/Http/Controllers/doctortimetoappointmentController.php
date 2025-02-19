<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Doctor_schedual_clinic;
use App\Models\Schedual;
use Carbon\Carbon;
use Illuminate\Http\Request;

class doctortimetoappointmentController extends Controller
{
    //
    public function get_doctor_time_to_do_appointment(Request $request)
    {

        $data = $request->validate([
            'doctor_id' => ['required']
        ]);
        if (!$data) {
            $message = ['message' => 'incourrct required'];
        } else {
            $doctor = Doctor::where('id', $request->doctor_id)->first();
            if (!$doctor) {
                $message = ['message' => 'incourrct id'];
            } else {
                $doctor_schedual_clinics = Doctor_schedual_clinic::where('doctor_id', $doctor->id)->get();
                if ($doctor_schedual_clinics->isempty()) {
                    $message = ['message' => 'no time'];
                } else {
                    $table = null;
                    foreach ($doctor_schedual_clinics as $doctor_schedual_clinic) {
                        if ($doctor_schedual_clinic->state == 'enabled') {
                            $schedual = Schedual::where('id', $doctor_schedual_clinic->schedual_id)->first();
                            $day = $schedual->day;
                            $time = null;
                            $st1 = Carbon::parse($schedual->start_time)->format('H:i:s');
                            $et1 = Carbon::parse($schedual->end_time)->format('H:i:s');
                            $time[] = $day;
                            while ($st1 < $et1 && Carbon::parse($st1)->addMinutes(40)->format('H:i:s') < $et1) {
                                $time[] = $st1;
                                $st1 = Carbon::parse($st1)->addMinutes(40)->format('H:i:s');
                            }
                            $table[] = $time;
                        }
                    }
                    $message = ['message'=>($table == null) ? 'no time' :[$table]];
                }

            }

        }
        return response()->json($message);
    }

}
