<?php

namespace App\Http\Controllers;

use App\Models\Schedual;
use Carbon\Carbon;
use Illuminate\Http\Request;

class schedualController extends Controller
{
    //
    public function addschedual(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $data = $request->validate([
                'day' => ['required', 'string'],
                'start_time' => ['required', 'string'],
                'end_time' => ['required', 'string'],
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $already = false;
                $scheds = Schedual::where('day', $request->day)->get();
                foreach ($scheds as $sched) {
                    if (Carbon::parse($sched->end_time)->format('H:i:s') == Carbon::parse($request->end_time)->format('H:i:s') && Carbon::parse($sched->start_time)->format('H:i:s') == Carbon::parse($request->start_time)->format('H:i:s')) {
                        $already = true;
                    }
                }
                if ($already) {
                    $message = [
                        'message' => 'it is already there'
                    ];
                } else {
                    $data['start_time']=Carbon::parse( $data['start_time'])->format('h:i:sa');
                    $data['end_time']=Carbon::parse( $data['end_time'])->format('h:i:sa');
                    $schedual = Schedual::create($data);
                    $message = [
                        'message' => 'schedual add successfully',
                        'schedual' => $schedual,
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
    public function getschedualbyid(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $filed = $request->validate([
                'id' => 'required',
            ]);
            $schedual = Schedual::where('id', $request->id)->first();
            if (!$schedual) {
                return response()->json('id incorrect', 422);
            } else {
                $schedual['start_time']=Carbon::parse($schedual['start_time'])->format('H:i:s');
                $schedual['end_time']=Carbon::parse($schedual['end_time'])->format('H:i:s');
                $response = [
                    'schedual' => $schedual,
                ];
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
        return response()->json($response);
    }
    public function editschedual(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $data = $request->validate([
                'id' => ['required'],
                'day' => ['required', 'string'],
                'start_time' => ['required', 'string'],
                'end_time' => ['required', 'string'],
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $already = false;
                $scheds = Schedual::where('day', $request->day)->get();
                foreach ($scheds as $sched) {
                    if (Carbon::parse($sched->end_time)->format('H:i:s') == Carbon::parse($request->end_time)->format('H:i:s') && Carbon::parse($sched->start_time)->format('H:i:s') == Carbon::parse($request->start_time)->format('H:i:s')) {
                        $already = true;
                    }
                }
                if ($already) {
                    $message = [
                        'message' => 'it is already there'
                    ];
                } else {
                    $schedual = Schedual::where('id', $request->id)->first();
                    $schedual->update($request->all());
                    $message = [
                        'message' => 'schedual updated successfully',
                        'schedual' => $schedual,
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
    public function getallscheduals(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $schedual = Schedual::paginate(10);
            if ($schedual->isempty() || !$schedual) {
                $response = [
                    'schedual' => 'id incorrect'
                ];
            } else {
                $response = [
                    'schedual' => $schedual,
                ];
                return response()->json($response);
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }
    }
}
