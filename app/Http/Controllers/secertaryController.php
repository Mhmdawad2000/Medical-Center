<?php

namespace App\Http\Controllers;

use App\Models\Secertary;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Http\Request;

class secertaryController extends Controller
{

    public function editsecertary(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {
            $data = $request->validate([
                'clinic_id' => ['required'],
                'id' => ['required'],
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {

                $secertary = Secertary::where('id', $request->id)->first();
                $secertary->update($request->all());
                $message = [
                    'message' => 'secertary updated successfully',
                    'secertary' => $secertary,
                ];
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role'
            ];
        }

        return response()->json($message);
    }
    public function getallsecertarys(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'admin') {

            $secertarys = Secertary::all();

            if ($secertarys->isempty()) {
                $response = [
                    'message' => 'no secertary founded'
                ];
            } else {
                $i = 1;
                foreach ($secertarys as $secertary) {

                    $allsecertarys[] = ["secertary$i" => [$secertary, User::where('id', $secertary->user_id)->first(), Clinic::where('secertaryid', $secertary->clinic_id)->first()]];
                    ++$i;
                }

                $response = [
                    'secertary' => $allsecertarys
                ];
            }
        } else {
            $message = [
                'message' => 'you don\'t have the role to create that user'
            ];
        }
        return response()->json($response);

    }
    public function makeuserblock(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'secertary') {
            $data = $request->validate([
                'id' => ['required'],
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $user = User::where('id', $request->id)->first();
                if ($user->user_type == 'patient') {
                    if (!$user) {
                        $message = [
                            'message' => 'not founded id'
                        ];
                    } else {
                        $user['state'] = 'block';
                        $user->update($request->all());
                        $message = [
                            'message' => 'user updated successfully',
                            'user' => $user,
                        ];
                    }
                } else {
                    $message = [
                        'message' => 'you dont have the role to bloke this type of user'
                    ];
                }
            }
        } else {
            $message = [
                'message' => 'you dont have the role'
            ];

        }
        return response()->json($message);
    }

    public function makeuseractive(Request $request)
    {
        $token = auth()->user()->user_type;
        if ($token == 'secertary') {
            $data = $request->validate([
                'id' => ['required'],
            ]);
            if (!$data) {
                $message = [
                    'message' => 'inccorect requireds'
                ];
            } else {
                $user = User::where('id', $request->id)->first();
                if ($user->user_type == 'patient') {
                    if (!$user) {
                        $message = [
                            'message' => 'not founded id'
                        ];
                    } else {
                        $user['state'] = 'active';
                        $user->update($request->all());
                        $message = [
                            'message' => 'user updated successfully',
                            'user' => $user,
                        ];
                    }
                } else {
                    $message = [
                        'message' => 'you dont have the role to active this type of user'
                    ];
                }
            }
        } else {
            $message = [
                'message' => 'you dont have the role'
            ];

        }
        return response()->json($message);
    }
}
