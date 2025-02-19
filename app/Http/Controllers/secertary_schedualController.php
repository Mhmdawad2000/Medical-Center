<?php

namespace App\Http\Controllers;

use App\Models\Schedual;
use App\Models\Secertary;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Http\Request;
use App\Models\Secertary_schedual;

class secertary_schedualController extends Controller
{
    //
    public function addsecertary_schedual(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $data = $request->validate([
                'secertary_id' => ['required'],
                'schedual_id' => ['required']
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $secertary = Secertary::where('id', $request->secertary_id)->first();
                $schedual = Schedual::where('id', $request->schedual_id)->first();
                if (!$schedual || !$secertary) {
                    $message = [
                        'message' => 'there are inccorect id\s'
                    ];
                } else {
                    $already = false;
                    $sece_scheds = Secertary_schedual::where('secertary_id', $request->secertary_id)->get();
                    foreach ($sece_scheds as $sece_sched) {
                        if ($sece_sched->schedual_id == $request->schedual_id) {
                            $already = true;
                        }
                    }
                    if ($already) {
                        $message = [
                            'message' => 'it is already there'
                        ];
                    } else {
                        $secertary_schedual = Secertary_schedual::create($data);
                        $message = [
                            'message' => 'secertary_schedual add successfully',
                            'secertary_schedual' => $secertary_schedual,
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

    public function editsecertary_schedual(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $data = $request->validate([
                'secertary_id' => ['required'],
                'schedual_id' => ['required'],
                'id' => ['required']
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {

                $secertary_schedual = Secertary_schedual::where('id', $request->id)->first();
                $secertary_schedual->update($request->all());
                $message = [
                    'message' => 'secertary updated successfully',
                    'secertary_schedual_table' => $secertary_schedual,
                ];
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($message);
    }
    public function getallsecertary_scheduals(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $secertary_scheduals = Secertary_schedual::paginate(10);
            if ($secertary_scheduals->isempty() || !$secertary_scheduals) {
                $response = ['secertary_schedual' => 'No secertary_scheduals'];
            } else {
                $table = null;
                foreach ($secertary_scheduals as $secertary_schedual) {
                    $secertary = Secertary::where('id', $secertary_schedual->secertary_id)->first();
                    $schedual = Schedual::where('id', $secertary_schedual->schedual_id)->first();
                    $user = User::where('id', $secertary->user_id)->first();
                    $table[] = [
                        'secertary_id' => $secertary->id,
                        'fname' => $user->fname,
                        'lname' => $user->lname,
                        'schedual_id' => $schedual->id,
                        'day' => $schedual->day,
                        'statr_time' => $schedual->start_time,
                        'end_time' => $schedual->end_time,
                    ];
                }
                $response = [
                    'secertary_schedual' => $table,
                ];

            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($response);
    } public function getsecertary_scheduals(Request $request)
    {
        $data=$request->validate([
            'secertary_id' => 'required',
        ]);
        $token = auth()->user()->user_type;
        if ($token == 'admin' || $token == 'secertary') {
            $secertary_scheduals = Secertary_schedual::where('secertary_id',$request->secertary_id)->get();
            if($secertary_scheduals->isempty()){
                $response = [];
            }else{
                $table = null;
                foreach ($secertary_scheduals as $secertary_schedual) {
                    $secertary = Secertary::where('id', $secertary_schedual->secertary_id)->first();
                    $schedual = Schedual::where('id', $secertary_schedual->schedual_id)->first();
                    $user = User::where('id', $secertary->user_id)->first();
                    $clinic=Clinic::where('id',$secertary->clinic_id)->first();
                    $table[] = [
                        'id'=>$secertary_schedual->id,
                        'secertary_id' => $secertary->id,
                        'fname' => $user->fname,
                        'lname' => $user->lname,
                        'schedual_id' => $schedual->id,
                        'clinic_id' => $clinic->id,
                        'clinic_num' => $clinic->clinic_num,
                        'day' => $schedual->day,
                        'start_time' => $schedual->start_time,
                        'end_time' => $schedual->end_time,
                    ];
                }
                $response = [
                    'secertary_schedual' => $table,
                ];

            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($response);
    }
}
