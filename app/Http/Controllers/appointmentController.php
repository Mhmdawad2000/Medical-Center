<?php

namespace App\Http\Controllers;

use App\Models\Secertary_schedual;
use App\Models\Specialization;
use App\Models\Appointment;
use App\Models\Appointment_medication;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Secertary;
use App\Models\Doctor_schedual_clinic;
use App\Models\Medication;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Schedual;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class appointmentController extends Controller
{
    public function addappointment(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'string'],
            'patient_id' => ['required'],
            'doctor_id' => ['required'],
            'description' => ['required', 'string'],
        ]);
        if (!$data) {
            $message = [
                'message' => 'inccorect requireds'
            ];
        } else {
            $token = auth()->user()->user_type;
            $token_id = auth()->user()->id;
            $patient = Patient::where('user_id', $token_id)->first();
            if ($token != 'admin' && $token != 'patient' || $token == 'admin' && $patient->id == $request->patient_id || $token == 'patient' && $patient->id == $request->patient_id) {
                $data['user_id'] = $token_id;
                $dateReq = $request->date;
                $date = Carbon::now()->format('Y-m-d H:i:s');
                $doctor = Doctor::where('id', $request->doctor_id)->first();
                $userdoctor = User::where('id', $doctor->user_id)->first();
                $patient = Patient::where('id', $request->patient_id)->first();
                $userpatient = User::where('id', $patient->user_id)->first();
                if ($userdoctor->state == 'active') {
                    if ($userpatient->state == 'active') {
                        if (Carbon::parse($date)->format('Y-m-d H:i:s') > Carbon::parse($dateReq)->format('Y-m-d H:i:s')) {
                            $message = [
                                'message' => 'Bad date'
                            ];
                        } else {
                            $appointments = Appointment::where('state', 'active')->get();
                            $i = 0;
                            $appointmentDate = true;
                            $appointmentpatient = true;
                            foreach ($appointments as $appointment) {
                                if ($request->patient_id == $appointment->patient_id) {
                                    ++$i;
                                }
                                $tdate1 = $appointment->date;
                                $date1 = Carbon::parse($tdate1)->format('Y-m-d H:i:s');
                                $tdate2 = $appointment->date;
                                $date2 = Carbon::parse($tdate2)->addMinutes(40)->format('Y-m-d H:i:s');
                                $tdate3 = $dateReq;
                                $date3 = Carbon::parse($tdate3)->format('Y-m-d H:i:s');
                                if ((($doctor->id == $appointment->doctor_id) || ($patient->id == $appointment->patient_id)) && ($date1 == $date3 || ($date1 < $date3 && $date2 > $date3))) {
                                    $appointmentDate = false;
                                    if ($patient->id == $appointment->patient_id) {
                                        $appointmentpatient = false;
                                    }
                                    break;
                                }
                            }
                            if ($appointmentDate) {
                                if ($i < 3) {
                                    $thisbool = true;
                                    if ($token == 'doctor') {
                                        $patient = Patient::where('user_id', $token_id)->first();
                                        if ($request->patient_id == $patient->id) {
                                            $doctor = Doctor::where('user_id', $token_id)->first();
                                            $thisdoctor_sch_clis = Doctor_schedual_clinic::where('doctor_id', $doctor->id)->get();
                                            foreach ($thisdoctor_sch_clis as $item) {
                                                $schedual = Schedual::where('id', $item->schedual_id)->first();
                                                $array_thisday = Carbon::parse($schedual->day)->toArray();
                                                $thisstart = Carbon::parse($schedual->start_time)->format('H:i:s');
                                                $thisend = Carbon::parse($schedual->end_time)->format('H:i:s');
                                                $thisdate = Carbon::parse($request->date)->format('H:i:s');
                                                $array_thisdate = Carbon::parse($request->date)->toArray();
                                                if ($item->state=='enabled'&&($array_thisdate['dayOfWeek'] == $array_thisday['dayOfWeek']) && (($thisdate >= $thisstart && $thisdate < $thisend) || ($thisdate > $thisstart && $thisdate <= $thisend))) {
                                                    $thisbool = false;
                                                    break;
                                                }
                                            }
                                        }
                                    } elseif ($token == 'secertary') {
                                        $patient = Patient::where('user_id', $token_id)->first();
                                        if ($request->patient_id == $patient->id) {
                                            $secertary = Secertary::where('user_id', $token_id)->first();
                                            $thissecertary_sch = Secertary_schedual::where('secertary_id', $secertary->id)->get();
                                            foreach ($thissecertary_sch as $item) {
                                                $schedual = Schedual::where('id', $item->schedual_id)->first();
                                                $array_thisday = Carbon::parse($schedual->day)->toArray();
                                                $thisstart = Carbon::parse($schedual->start_time)->format('H:i:s');
                                                $thisend = Carbon::parse($schedual->end_time)->format('H:i:s');
                                                $thisdate = Carbon::parse($request->date)->format('H:i:s');
                                                $array_thisdate = Carbon::parse($request->date)->toArray();
                                                if (($array_thisdate['dayOfWeek'] == $array_thisday['dayOfWeek']) && (($thisdate >= $thisstart && $thisdate < $thisend) || ($thisdate > $thisstart && $thisdate <= $thisend))) {
                                                    $thisbool = false;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    if ($thisbool) {
                                        $appointment = Appointment::create($data);
                                        $message = [
                                            'message' => 'Appointment add successfully',
                                            'appointment' => $appointment
                                        ];
                                    } else {
                                        $message = [
                                            'message' => 'you have schedual work in this date ):'
                                        ];
                                    }
                                } else {
                                    $message = [
                                        'message' => 'The patient has 3 appointments'
                                    ];
                                }
                            } else {
                                if (!$appointmentpatient) {
                                    $message = [
                                        'message' => 'there is appointment in this time',
                                        'appointment' => $appointment
                                    ];
                                } else {
                                    $message = [
                                        'message' => 'doctor has appointment in this date & time',
                                    ];
                                }
                            }
                        }
                    } elseif ($userpatient->state == 'pending') {
                        if (Carbon::parse($date)->format('Y-m-d H:i:s') > Carbon::parse($dateReq)->format('Y-m-d H:i:s')) {
                            $message = [
                                'message' => 'Bad date'
                            ];
                        } else {
                            $appointments = Appointment::where('state', 'active')->get();
                            $appointment = Appointment::class;
                            $i = 0;
                            $appointmentDate = true;
                            foreach ($appointments as $appointment) {
                                if ($request->patient_id == $appointment->patient_id) {
                                    ++$i;
                                }
                                $tdate1 = $appointment->date;
                                $date1 = Carbon::parse($tdate1)->format('Y-m-d H:i:s');
                                $tdate2 = $appointment->date;
                                $date2 = Carbon::parse($tdate2)->addMinutes(30)->format('Y-m-d H:i:s');
                                $tdate3 = $dateReq;
                                $date3 = Carbon::parse($tdate3)->format('Y-m-d H:i:s');
                                if (($doctor->id == $appointment->doctor_id) && ($date1 == $date3 || ($date1 < $date3 && $date2 > $date3))) {
                                    $appointmentDate = false;
                                    break;
                                }
                            }
                            if ($appointmentDate) {
                                if ($i < 1) {
                                    $appointment = Appointment::create($data);
                                    $message = [
                                        'message' => 'Appointment add successfully',
                                        'appointment' => $appointment
                                    ];
                                } else {
                                    $message = [
                                        'message' => 'The patient state is pending ,can\'t take more one appointment'
                                    ];
                                }
                            } else {
                                $message = [
                                    'message' => 'there is appointment in this time',
                                    'appointment' => $appointment
                                ];
                            }
                        }
                    } else {
                        $message = [
                            'message' => 'Patient not enabeld'
                        ];
                    }
                } else {
                    $message = [
                        'message' => 'Doctor not enabeld'
                    ];
                }
            } else {
                $message = [
                    'message' => 'you don\'t have the role'
                ];
            }
        }
        return response()->json($message);
    }
    public function editappointment_date(Request $request)
    {

        $data = $request->validate([
            'id' => ['required'],
            'date' => ['required', 'string']
        ]);
        if (!$data) {
            $message = [
                'message' => 'inccorect requireds'
            ];
        } else {
            $token = auth()->user()->user_type;
            $token_id = auth()->user()->id;
            $patient = Patient::where('user_id', $token_id)->first();
            $editapp = Appointment::where('id', $request->id)->first();
            if ($editapp->patient_id == $patient->id || $token == 'secertary' || $token == 'doctor') {
                if (!$editapp) {
                    $message = [
                        'message' => 'inccorect id'
                    ];
                } else {
                    if ($editapp->state == 'active') {
                        $dateReq = $request->date;
                        $date = Carbon::now()->format('Y-m-d H:i:s');
                        $doctor = Doctor::where('id', $editapp->doctor_id)->first();
                        $userdoctor = User::where('id', $doctor->user_id)->first();
                        $patient = Patient::where('id', $editapp->patient_id)->first();
                        $userpatient = User::where('id', $patient->user_id)->first();
                        if ($userdoctor->state == 'active') {
                            if ($userpatient->state == 'active') {
                                if ($date > $dateReq) {
                                    $message = [
                                        'message' => 'Bad date'
                                    ];
                                } else {
                                    $appointments = Appointment::where('state', 'active')->get();
                                    $i = 0;
                                    $appointmentDate = true;
                                    $appointmentpatient = true;
                                    foreach ($appointments as $appointment) {
                                        if ($editapp->patient_id == $appointment->patient_id) {
                                            ++$i;
                                            if ($appointment->id == $request->id) {
                                                --$i;
                                            }
                                        }
                                        $datetime1 = explode(' ', $appointment->date);
                                        $datetime2 = explode(' ', $dateReq);
                                        $date1 = explode('-', $datetime1[0]);
                                        $time1 = explode(':', $datetime1[1]);
                                        $date2 = explode('-', $datetime2[0]);
                                        $time2 = explode(':', $datetime2[1]);
                                        if (($patient->id == $appointment->patient_id || $doctor->id == $appointment->doctor_id) && $date1[0] == $date2[0] && $date1[1] == $date2[1] && $date1[2] == $date2[2] && $time1[0] == $time2[0] && ($time1[1] == $time2[1] || $time1[1] < 30 + $time2[1]) && $time1[2] == $time2[2]) {
                                            $appointmentDate = false;
                                            if ($patient->id == $appointment->patient_id) {
                                                $appointmentpatient = false;
                                            }
                                            break;
                                        }
                                    }
                                    if ($appointmentDate) {
                                        if ($i < 3) {
                                            $thisbool = true;
                                            $dateupdate = $editapp->updated_at->format('Y-m-d H:i:s');
                                            $datecreate = $editapp->created_at->format('Y-m-d H:i:s');
                                            if ($datecreate != $dateupdate) {
                                                $message = ['message' => 'Appointment updated befor'];
                                            } else {
                                                if ($token == 'doctor') {
                                                    $patient = Patient::where('user_id', $token_id)->first();
                                                    if ($editapp->patient_id == $patient->id) {
                                                        $doctor = Doctor::where('user_id', $token_id)->first();
                                                        $thisdoctor_sch_clis = Doctor_schedual_clinic::where('doctor_id', $doctor->id)->get();
                                                        foreach ($thisdoctor_sch_clis as $item) {
                                                            $schedual = Schedual::where('id', $item->schedual_id)->first();
                                                            $array_thisday = Carbon::parse($schedual->day)->toArray();
                                                            $thisstart = Carbon::parse($schedual->start_time)->format('H:i:s');
                                                            $thisend = Carbon::parse($schedual->end_time)->format('H:i:s');
                                                            $thisdate = Carbon::parse($request->date)->format('H:i:s');
                                                            $array_thisdate = Carbon::parse($request->date)->toArray();
                                                            if ($item->state=='enabled'&&($array_thisdate['dayOfWeek'] == $array_thisday['dayOfWeek']) && (($thisdate >= $thisstart && $thisdate < $thisend) || ($thisdate > $thisstart && $thisdate <= $thisend))) {
                                                                $thisbool = false;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                } elseif ($token == 'secertary') {
                                                    $patient = Patient::where('user_id', $token_id)->first();
                                                    if ($editapp->patient_id == $patient->id) {
                                                        $secertary = Secertary::where('user_id', $token_id)->first();
                                                        $thissecertary_sch = Secertary_schedual::where('secertary_id', $secertary->id)->get();
                                                        foreach ($thissecertary_sch as $item) {
                                                            $schedual = Schedual::where('id', $item->schedual_id)->first();
                                                            $array_thisday = Carbon::parse($schedual->day)->toArray();
                                                            $thisstart = Carbon::parse($schedual->start_time)->format('H:i:s');
                                                            $thisend = Carbon::parse($schedual->end_time)->format('H:i:s');
                                                            $thisdate = Carbon::parse($request->date)->format('H:i:s');
                                                            $array_thisdate = Carbon::parse($request->date)->toArray();
                                                            if (($array_thisdate['dayOfWeek'] == $array_thisday['dayOfWeek']) && (($thisdate >= $thisstart && $thisdate < $thisend) || ($thisdate > $thisstart && $thisdate <= $thisend))) {
                                                                $thisbool = false;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                                if ($thisbool) {
                                                    $editapp->update($request->all());
                                                    $message = [
                                                        'message' => 'Appointment update successfully',
                                                        'appointment' => $editapp
                                                    ];
                                                    $patient = Patient::where('id', $editapp->patient_id)->first();
                                                    $message2 = [
                                                        'user_id' => $patient->user_id,
                                                        'title' => 'your appointment date updated  {id=' . $editapp->id . ' ,date=' . $editapp->date . '}'
                                                    ];
                                                    Message::create($message2);
                                                    $message = [
                                                        'message' => 'appointment updeted sucssecfully',
                                                        'appointment' => $appointment,
                                                        'user_message' => $message2
                                                    ];
                                                } else {
                                                    $message = [
                                                        'message' => 'you have schedual work in this date ):'
                                                    ];
                                                }
                                            }
                                        } else {
                                            $message = [
                                                'message' => 'The patient has 3 appointments'
                                            ];
                                        }
                                    } else {
                                        if (!$appointmentpatient) {
                                            $message = [
                                                'message' => 'there is appointment in this time',
                                                'appointment' => $appointment
                                            ];
                                        } else {
                                            $message = [
                                                'message' => 'doctor has appointment in this date & time',
                                            ];
                                        }
                                    }
                                }
                            } else {
                                $message = [
                                    'message' => 'Patient not enabeld'
                                ];
                            }
                        } else {
                            $message = [
                                'message' => 'Doctor not enabeld'
                            ];
                        }
                    } else {
                        $message = [
                            'message' => 'the appointment state not active'
                        ];
                    }
                }
            } else {
                $message = [
                    'message' => 'you don\'t have the role'
                ];
            }
        }
        return response()->json($message);
    }
    public function get_all_active_appointment_to_patient(Request $request)
    {
        $token_id = auth()->user()->id;
        $mymessages = Message::where('user_id', $token_id)->get();
        $patient = Patient::where('user_id', $token_id)->first();
        $appointments = Appointment::where('patient_id', $patient->id)->get();
        if ($appointments->isempty()) {
            $response = [
                'message' => 'no appointments',
                'Mymessages' => $mymessages
            ];
        } else {
            $allappointments = null;
            foreach ($appointments as $appointment) {
                if ($appointment['state'] == 'active') {
                    $Date = Carbon::parse($appointment->date)->toArray();
                    $doc_sch_cli = Doctor_schedual_clinic::where('doctor_id', $appointment->doctor_id)->get();
                    $cli = null;
                    foreach ($doc_sch_cli as $item) {
                        $schedual = Schedual::where('id', $item->schedual_id)->first();
                        $schedual2 = Carbon::parse($schedual->day)->toArray();
                        $schedualstart = Carbon::parse($schedual->start_time)->format('H:i:s');
                        $schedualend = Carbon::parse($schedual->end_time)->format('H:i:s');
                        if ($Date['dayOfWeek'] == $schedual2['dayOfWeek']) {
                            $Date = Carbon::parse($appointment->date)->format('H:i:s');
                            if ($Date < $schedualend && $Date >= $schedualstart) {
                                $cli = $item->clinic_id;
                                $cli = Clinic::where('id', $cli)->first();
                                $cli = $cli->clinic_num;
                                break;
                            }
                        }
                    }
                    $allappointments[] = ['Appointment' => $appointment, 'Clinic' => $cli];
                }
            }

            $response = [
                'appointments' => ($allappointments == null) ? 'no appointments' : $allappointments,
                'Mymessages' => $mymessages
            ];
        }
        return response()->json($response);
    }

    public function getappintmentbyid(Request $request)
    {
        $falid = $request->validate([
            'id' => 'required'
        ]);
        $appointment = Appointment::where('id', $request->id)->first();
        if (!$appointment) {
            return response()->json('id incorrect', 422);
        } else {
            $patient = Patient::where('id', $appointment->patient_id)->first();
            $user = User::where('id', $patient->user_id)->first();
            $token_id = auth()->user()->id;
            $token = auth()->user()->user_type;
            if ($token == 'secertary' || $token == 'doctor' || $token_id == $user->id) {
                $response = [
                    'appointment' => $appointment,
                ];
            } else {
                $response = [
                    'message' => 'you don\'t have the role'
                ];
            }
        }
        return response()->json($response);
    }
    public function edit_description_appointment(Request $request)
    {
        $token = auth()->user()->user_type;
        $token_id = auth()->user()->id;
        $data = $request->validate([
            'id' => ['required'],
            'description' => ['required', 'string']
        ]);
        if (!$data) {
            $message = ['message' => 'inccorect required'];
        } else {
            $appointment = Appointment::where('id', $request->id)->first();
            $doctor = Doctor::where('user_id', $token_id)->first();
            if (!$appointment) {
                $message = ['message' => 'inccorect id'];
            } else {
                if ($token == 'doctor' && $doctor->id == $appointment->doctor_id) {
                    if ($appointment['state'] == 'active') {
                        $appointment['description'] = $request->description;
                        $appointment->update();
                        $message = [
                            'message' => 'appointment state updated',
                            'appointment' => $appointment
                        ];
                    } else {
                        $message = [
                            'message' => 'you can\'t update the appointment',
                        ];
                    }
                } else {
                    $message = [
                        'message' => 'you don\'t have the role'
                    ];
                }
            }
        }
        return response()->json($message);
    }

    public function editstateappointmenttodone(Request $request)
    {
        $data = $request->validate([
            'id' => ['required']
        ]);
        if (!$data) {
            $message = ['message' => 'inccorect required'];
        } else {
            $appointment = Appointment::where('id', $request->id)->first();
            $token = auth()->user()->user_type;
            $token_id = auth()->user()->id;
            $doctor = Doctor::where('user_id', $token_id)->first();
            if ($token == 'doctor' && $doctor->id == $appointment->doctor_id) {
                if (!$appointment) {
                    $message = ['message' => 'inccorect id'];
                } else {
                    if ($appointment->state == 'active') {

                        $patient = Patient::where('id', $appointment->patient_id)->first();
                        if ($patient['counter_to_block'] > 0) {
                            $patient['counter_to_block'] -= 1;
                            $patient->update();
                        }

                        $user = User::where('id', $patient->user_id)->first();
                        if ($user->state == 'pending') {
                            $user['state'] = 'active';
                            $user->update();
                        }

                        $appointment['state'] = 'done';
                        $appointment->update();
                        $message = [
                            'message' => 'appointment state updated to done',
                            'appointment' => $appointment
                        ];
                    } else {
                        $message = ['message' => 'appointment state not active'];
                    }
                }
            } else {
                $message = [
                    'message' => 'you don\'t have the role'
                ];
            }
        }
        return response()->json($message);
    }

    public function editstateappointmenttoundone(Request $request)
    {
        $data = $request->validate([
            'id' => ['required']
        ]);
        if (!$data) {
            $message = ['message' => 'inccorect required'];
        } else {
            $appointment = Appointment::where('id', $request->id)->first();
            if (!$appointment) {
                $message = ['message' => 'inccorect id'];
            } else {
                $token = auth()->user()->user_type;
                $token_id = auth()->user()->id;
                $doctor = Doctor::where('user_id', $token_id)->first();
                if ($token == 'doctor' && $doctor->id == $appointment->doctor_id) {
                    if ($appointment->state == 'active') {
                        $patient = Patient::where('id', $appointment->patient_id)->first();
                        $patient['counter_to_block'] += 1;
                        $patient->update();

                        if ($patient['counter_to_block'] == 3) {
                            $user = User::where('id', $patient->user_id)->first();
                            if ($user->user_type != 'admin') {
                                $user['state'] = 'block';
                                $user->update();
                            }
                        }

                        $user = User::where('id', $patient->user_id)->first();
                        if ($user->state == 'pending') {
                            $user['state'] = 'block';
                            $user->update();
                        }

                        $appointment['state'] = 'undone';
                        $appointment->update();
                        $message = [
                            'message' => 'appointment state updated to undone',
                            'appointment' => $appointment
                        ];
                    } else {
                        $message = ['message' => 'appointment state not active'];
                    }
                } else {
                    $message = [
                        'message' => 'you don\'t have the role'
                    ];
                }
            }
        }
        return response()->json($message);
    }
    public function get_all_active_appointment_to_dotor(Request $request)
    {
        $falid = $request->validate([
            'doctor_id' => 'required'
        ]);
        $doctor = Doctor::where('id', $request->doctor_id)->first();
        $appointments = Appointment::where('doctor_id', $request->doctor_id)->get();
        if (!$doctor) {
            $response = ['message' => 'id incorrect'];
        } else {
            $token = auth()->user()->user_type;
            $token_id = auth()->user()->id;
            if ($token == 'doctor' && $token_id == $doctor->user_id || $token == 'secertary') {
                if ($appointments->isempty()) {
                    $response = [
                        'message' => 'no appointments',
                    ];
                } else {
                    $allappointments = null;
                    foreach ($appointments as $appointment) {
                        if ($appointment['state'] == 'active') {
                            $Date = Carbon::parse($appointment->date)->toArray();
                            $doc_sch_cli = Doctor_schedual_clinic::where('doctor_id', $appointment->doctor_id)->get();
                            $cli = null;
                            foreach ($doc_sch_cli as $item) {
                                $schedual = Schedual::where('id', $item->schedual_id)->first();
                                $schedual2 = Carbon::parse($schedual->day)->toArray();
                                $schedualstart = Carbon::parse($schedual->start_time)->format('H:i:s');
                                $schedualend = Carbon::parse($schedual->end_time)->format('H:i:s');
                                if ($Date['dayOfWeek'] == $schedual2['dayOfWeek']) {
                                    $Date = Carbon::parse($appointment->date)->format('H:i:s');
                                    if ($Date < $schedualend && $Date >= $schedualstart) {
                                        $cli = $item->clinic_id;
                                        $cli = Clinic::where('id', $cli)->first();
                                        $cli = $cli->clinic_num;
                                        break;
                                    }
                                }
                            }
                            $patient = Patient::where('id', $appointment->patient_id)->first();
                            $puser = User::where('id', $patient->user_id)->first();
                            $doctor = Doctor::where('id', $appointment->doctor_id)->first();
                            $duser = User::where('id', $doctor->user_id)->first();
                            $allappointments[] = ['Doctor_name' => $duser->fname . " " . $duser->lname, 'Patient_name' => $puser->fname . " " . $puser->lname, 'Appointment' => $appointment, 'Clinic' => $cli];
                        }
                    }
                    $response = [
                        'appointments' => ($allappointments == null) ? 'no appointments' : $allappointments,
                    ];
                }
            } else {
                $response = [
                    'message' => 'you don\'t have the role'
                ];
            }
        }
        return response()->json($response);
    }
    public function get_all_active_appointment_to_secertary(Request $request)
    {
        $token = auth()->user()->user_type;
        $token_id = auth()->user()->id;
        $secertary = Secertary::where('user_id', $token_id)->first();
        $clinic = Clinic::where('id', $secertary->clinic_id)->first();
        $appointments = Appointment::where('state', 'active')->get();
        if (!$secertary) {
            $response = ['message' => 'id incorrect'];
        } elseif (!$clinic) {
            $response = ['message' => 'id incorrect'];
        } else {
            $token = auth()->user()->user_type;
            $token_id = auth()->user()->id;
            if ($token == 'secertary' && $token_id == $secertary->user_id) {
                if ($appointments->isempty()) {
                    $response = [
                        'message' => 'no appointments',
                    ];
                } else {
                    $allappointments = null;
                    foreach ($appointments as $appointment) {

                        $doc_sch_cli = Doctor_schedual_clinic::where('doctor_id', $appointment->doctor_id)->get();
                        foreach ($doc_sch_cli as $item) {
                            if ($item->clinic_id == $clinic->id) {
                                $patient = Patient::where('id', $appointment->patient_id)->first();
                                $puser = User::where('id', $patient->user_id)->first();
                                $doctor = Doctor::where('id', $appointment->doctor_id)->first();
                                $duser = User::where('id', $doctor->user_id)->first();
                                $allappointments[] = ['Doctor_id' => $doctor->id, 'Doctor_name' => $duser->fname . " " . $duser->lname, 'Patient_name' => $puser->fname . " " . $puser->lname, 'Appointment_id' => $appointment->id, 'Date' => $appointment->date];;
                            }
                        }
                    }
                    $response = [
                        'appointments' => ($allappointments == null) ? 'no appointments' : $allappointments,
                    ];
                }
            } else {
                $response = [
                    'message' => 'you don\'t have the role'
                ];
            }
        }
        return response()->json($response);
    }

    public function get_medical_profile(Request $request)
    {
        $token = auth()->user()->user_type;
        $token_id = auth()->user()->id;
        $falid = $request->validate([
            'patient_id' => 'required'
        ]);
        $patient = Patient::where('id', $request->patient_id)->first();
        $user = User::where('id', $patient->user_id)->first();
        $appointments = Appointment::where('patient_id', $request->patient_id)->get();
        if (!$patient) {
            $response = ['message' => 'id incorrect'];
        } else {
            if ($token == 'doctor' || $token_id == $user->id) {

                if ($appointments->isempty()) {
                    $response = [
                        'fname' => $user->fname,
                        'lname' => $user->lname,
                        'notes' => $user->notes,
                        'message' => 'no appointments',
                    ];
                } else {
                    $allappointments = null;
                    foreach ($appointments as $appointment) {
                        $medications = null;
                        if ($appointment['state'] == 'done') {
                            $appointment_medications = Appointment_medication::where('appointment_id', $appointment->id)->get();
                            foreach ($appointment_medications as $appointment_medication) {
                                $medication = Medication::where('id', $appointment_medication->medication_id)->first();
                                $medications[] = $medication->medication_name;
                            }
                            $allappointments[] = [
                                'Date' => $appointment->date,
                                'Description' => $appointment->description,
                                'Medications' => $medications,
                            ];
                        }
                    }
                    $response = [
                        'fname' => $user->fname,
                        'lname' => $user->lname,
                        'notes' => $user->notes,
                        'appointments' => ($allappointments == null) ? 'no appointments' : $allappointments,
                    ];
                }
            } else {
                $response = [
                    'message' => 'you don\'t have the role'
                ];
            }
        }
        return response()->json($response);
    }
    public function get_personal_profile(Request $request)
    {
        $token_id = auth()->user()->id;
        $user = User::where('id', $token_id)->first();
        if (!$user) {
            $response = ['message' => 'id incorrect'];
        } else {
            $response = [
                'user' => [
                    'id' => $user->id,
                    'state' => $user->state,
                    'fname' => $user->fname,
                    'lname' => $user->lname,
                    'birthday' => $user->birthday,
                    'gendere' => $user->gendere,
                    'address' => $user->address,
                    'email' => $user->email,
                    'nationality' => $user->nationality,
                    'phone' => $user->phone,
                ]
            ];
        }
        return response()->json($response);
    }

    public function get_all_done_appointment_to_patient(Request $request)
    {
        $token_id = auth()->user()->id;
        $patient = Patient::where('user_id', $token_id)->first();
        $appointments = Appointment::where('patient_id', $patient->id)->get();
        if ($appointments->isempty()) {
            $response = [
                'message' => 'no appointments',
            ];
        } else {
            $allappointments = null;
            foreach ($appointments as $appointment) {
                if ($appointment['state'] == 'done') {
                    $Date = Carbon::parse($appointment->date)->toArray();
                    $doc_sch_cli = Doctor_schedual_clinic::where('doctor_id', $appointment->doctor_id)->get();
                    $cli = null;
                    foreach ($doc_sch_cli as $item) {
                        $schedual = Schedual::where('id', $item->schedual_id)->first();
                        $schedual2 = Carbon::parse($schedual->day)->toArray();
                        $schedualstart = Carbon::parse($schedual->start_time)->format('H:i:s');
                        $schedualend = Carbon::parse($schedual->end_time)->format('H:i:s');
                        if ($Date['dayOfWeek'] == $schedual2['dayOfWeek']) {
                            $Date = Carbon::parse($appointment->date)->format('H:i:s');
                            if ($Date < $schedualend && $Date >= $schedualstart) {
                                $cli = $item->clinic_id;
                                $cli = Clinic::where('id', $cli)->first();
                                $cli = $cli->clinic_num;
                                break;
                            }
                        }
                    }
                    $doctor = Doctor::where('id', $appointment->doctor_id)->first();
                    $specialization = Specialization::where('id', $doctor->specialization_id)->first();
                    $user = User::where('id', $doctor->user_id)->first();
                    $patient_user = User::where('id', $patient->user_id)->first();
                    $allappointments[] = ['Doctor_name' => $user->fname . " " . $user->lname, 'specialization' => $specialization->specialization_type, 'patient_name' => $patient_user->fname . " " . $patient_user->lname, 'Appointment' => $appointment, 'Clinic' => $cli];
                }
            }
            $response = [
                'appointments' => ($allappointments == null) ? 'no appointments' : $allappointments,
            ];
        }
        return response()->json($response);
    }
    public function deleteappointment(Request $request)
    {

        $date = $request->validate([
            'id' => ['required']
        ]);
        if (!$date) {
            $message = ['message' => 'id required'];
        } else {
            $appointment = Appointment::where('id', $request->id)->first();
            $token = auth()->user()->id;
            $patient = Patient::where('user_id', $token)->first();
            if ($patient->id == $appointment->patient_id) {
            } else {
                $message = [
                    'message' => 'you don\'t have the role'
                ];
            }
            if (!$appointment) {
                $message = ['message' => 'incourrect id'];
            } else {
                if ($appointment['state'] == 'active') {
                    $patient = Patient::where('id', $request->id)->first();
                    $message = [
                        'user_id' => $patient->user_id,
                        'title' => 'your appointment deleted id=' . $appointment->id . ' ,date=' . $appointment->date
                    ];
                    Message::create($message);
                    $appointment->delete();
                    $message = ['message' => 'appointment deleted successfully'];
                } else {
                    $message = ['message' => 'you can\'t delete appointment because state not active'];
                }
            }
        }
        return response()->json($message);
    }
}
