<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Doctor_schedual_clinic;
use App\Models\Patient;
use App\Models\Schedual;
use App\Models\Secertary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class docotr_schedual_clinicController extends Controller
{
    //
    public function adddoctor_schedual_clinic(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $data = $request->validate([
                'doctor_id' => ['required'],
                'schedual_id' => ['required'],
                'clinic_id' => ['required']

            ]);
            $schedual = Schedual::where('id', $request->schedual_id)->first();
            $doctor = Doctor::where('id', $request->doctor_id)->first();
            $clinic = Clinic::where('id', $request->clinic_id)->first();
            if (!$clinic || !$doctor || !$schedual) {
                $message = [
                    'message' => 'inccorect id'
                ];
            } else {
                if (!$data) {
                    $message = [
                        'message' => 'inccorect requireds'
                    ];
                } else {
                    $already = false;
                    $doc_sched_clins = Doctor_schedual_clinic::where('doctor_id', $request->doctor_id)->orwhere('clinic_id', $request->clinic_id)->get();
                    $schedual2 = Schedual::where('id', $request->schedual_id)->first();
                    $start_time2 = Carbon::parse($schedual2['start_time'])->format("H:i:s");
                    $end_time2 = Carbon::parse($schedual2['end_time'])->format("H:i:s");
                    foreach ($doc_sched_clins as $doc_sched_clin) {
                        $schedual1 = Schedual::where('id', $doc_sched_clin->schedual_id)->first();
                        $start_time1 = Carbon::parse($schedual1['start_time'])->format("H:i:s");
                        $end_time1 = Carbon::parse($schedual1['end_time'])->format("H:i:s");
                        if ($schedual1['day'] == $schedual2['day'] && (($start_time2 >= $start_time1 && $start_time2 < $end_time1) || ($end_time2 >= $start_time1 && $end_time2 < $end_time1))) {
                            $already = true;
                            break;
                        }
                        if ($doc_sched_clin->schedual_id == $request->schedual_id) {
                            $already = true;
                            break;
                        }
                    }
                    if ($already) {
                        $message = [
                            'message' => 'Bad time'
                        ];
                    } else {
                        $doctor_schedual_clinic = Doctor_schedual_clinic::create($data);
                        $message = [
                            'message' => 'Doctor_schedual_clinic add successfully',
                            'Doctor_schedual_clinic' => $doctor_schedual_clinic,
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
    public function editDoctor_schedual_clinicbyid_to_enabled(Request $request)
    {
        $token = auth()->user()->user_type;
        $token_id = auth()->user()->id;
        if ($token == 'secertary') {
            $filed = $request->validate([
                'id' => ['required'],
                'date' => ['required', 'string']
            ]);
            $secertary = secertary::where('user_id', $token_id)->first();
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $Doctor_schedual_clinic = Doctor_schedual_clinic::where('id', $request->id)->first();
            if (!$Doctor_schedual_clinic) {
                return response()->json('id incorrect', 422);
            } else {
                $appointments = null;
                $doctor = Doctor::where('id', $Doctor_schedual_clinic->doctor_id)->first();
                $patient = Patient::where('user_id', $doctor->user_id)->first();
                $appointments = Appointment::where('patient_id', $patient->id)->get();
                if ($appointments != null) {
                    foreach ($appointments as $appointment) {
                        if ($appointment->state == 'active') {
                            if (Carbon::parse($appointment->date)->format('Y-m-d') == $date) {
                                $patient = Patient::where('id', $appointment->patient_id)->first();
                                $message = [
                                    'user_id' => $patient->user_id,
                                    'title' => 'your appointment deleted id=' . $appointment->id . ' ,date=' . $appointment->date
                                ];
                                Message::create($message);
                                $appointment->delete();
                            }

                        }
                    }
                }
                if ($secertary->clinic_id == $Doctor_schedual_clinic->clinic_id) {
                    $Doctor_schedual_clinic['state'] = 'enabled';
                    $Doctor_schedual_clinic->update();
                    $response = [
                        'message' => 'Doctor_schedual_clinic state updated',
                        'Doctor_schedual_clinic' => $Doctor_schedual_clinic,
                    ];
                } else {
                    $response = [
                        'message' => 'you aren\'t on this day with this doctor',
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
    public function editDoctor_schedual_clinicbyid_to_disabled(Request $request)
    {
        $token = auth()->user()->user_type;
        $token_id = auth()->user()->id;
        if ($token == 'secertary') {
            $filed = $request->validate([
                'id' => 'required',
                'date' => 'required',
            ]);
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $Doctor_schedual_clinic = Doctor_schedual_clinic::where('id', $request->id)->first();
            if (!$Doctor_schedual_clinic) {
                return response()->json('id incorrect', 422);
            } else {
                $appointments = null;
                $appointments = Appointment::where('doctor_id', $Doctor_schedual_clinic->doctor_id)->get();
                if ($appointments != null) {
                    foreach ($appointments as $appointment) {
                        if ($appointment->state == 'active') {
                            if (Carbon::parse($appointment->date)->format('Y-m-d') == $date) {
                                $patient = Patient::where('id', $appointment->patient_id)->first();
                                $message = [
                                    'user_id' => $patient->user_id,
                                    'title' => 'your appointment deleted id=' . $appointment->id . ' ,date=' . $appointment->date
                                ];
                                Message::create($message);
                                $appointment->delete();
                            }

                        }
                    }
                }
                $secertary = Secertary::where('user_id', $token_id)->first();
                if ($secertary->clinic_id == $Doctor_schedual_clinic->clinic_id) {
                    $Doctor_schedual_clinic['state'] = 'disabled';
                    $Doctor_schedual_clinic->update();
                    $response = [
                        'message' => 'Doctor_schedual_clinic state updated',
                        'Doctor_schedual_clinic' => $Doctor_schedual_clinic,
                    ];
                } else {
                    $response = [
                        'message' => 'you aren\'t on this day with this doctor',
                    ];
                }
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($response);
    }
    public function getDoctor_schedual_clinicbyid(Request $request)
    {

        $filed = $request->validate([
            'id' => 'required',
        ]);

        $doctor = Doctor::where('id', $request->id)->first();
        if (!$doctor) {
            return response()->json('id incorrect', 422);
        } else {
            $Doctor_schedual_clinics = null;
            $days = [];
            $Doctor_schedual_clinics = Doctor_schedual_clinic::where('doctor_id', $doctor->id)->get();
            if ($Doctor_schedual_clinics == null) {
                $response = ['message' => 'no Scheduals'];
            } else {
                foreach ($Doctor_schedual_clinics as $item) {
                    $days[] = [
                        'id' => $item->id,
                        'doctor_id' => $item->doctor_id,
                        'clinic_id' => $item->clinic_id,
                        'schedual_id' => $item->schedual_id,
                        'day' => Schedual::where('id', $item->schedual_id)->first()->day,
                        'start_time' => Schedual::where('id', $item->schedual_id)->first()->start_time,
                        'end_time' => Schedual::where('id', $item->schedual_id)->first()->end_time,
                        'clinic_num' => Clinic::where('id', $item->clinic_id)->first()->clinic_num,
                    ];
                }
            }
            $response = ['Doctor_Shedual_Clinic' => $days];
        }
        return response()->json($response);
    }

    public function editDoctor_schedual_clinic(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $data = $request->validate([
                'doctor_id' => ['required'],
                'schedual_id' => ['required'],
                'clinic_id' => ['required'],
                'id' => ['required']
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $already = false;
                $doc_sched_clins = Doctor_schedual_clinic::where('doctor_id', $request->doctor_id)->orwhere('clinic_id', $request->clinic_id)->get();
                foreach ($doc_sched_clins as $doc_sched_clin) {
                    if ($doc_sched_clin->schedual_id == $request->schedual_id) {
                        $already = true;
                        break;
                    }
                }
                if ($already) {
                    $message = [
                        'message' => 'it is already there'
                    ];
                } else {
                    $Doctor_schedual_clinic = Doctor_schedual_clinic::where('id', $request->id)->first();
                    $Doctor_schedual_clinic->update($request->all());
                    $message = [
                        'message' => 'doctor schedual updated successfully',
                        'Doctor_schedual_clinic_table' => $Doctor_schedual_clinic,
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
    public function getallDoctor_schedual_clinics(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $Doctor_schedual_clinic = Doctor_schedual_clinic::paginate(10);
            if ($Doctor_schedual_clinic->isempty() || !$Doctor_schedual_clinic) {
                $response = ['Doctor_schedual_clinic' => 'No Doctor_schedual_clinics'];
            } else {
                $response = [
                    'Doctor_schedual_clinic' => $Doctor_schedual_clinic,
                ];

            }
        } else {
            $response = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($response);
    }
}
