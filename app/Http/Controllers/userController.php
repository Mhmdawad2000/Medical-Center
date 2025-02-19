<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    //
    public function edituser(Request $request)
    {
        $data = $request->validate([
            'fname' => ['required', 'string'],
            'lname' => ['required', 'string'],
            'birthday' => ['required', 'string'],
            'gendere' => ['required', 'string'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'nationality' => ['required', 'string'],
            'national_id' => ['required', 'numeric', 'unique:users,national_id'],
            'notes' => ['required', 'string']
        ]);
        $user_type = auth()->user()->user_type;
        $user_id = auth()->user()->id;
        if ($user_type == 'admin' || $user_type == 'patient') {
            $user = User::where('id', $user_id)->first();
            if (!$data) {
                $message = ['message' => 'incorrect required '];
            } else {
                $data['birthday']=Carbon::parse($data['birthday'])->format('Y-m-d');
                $user->update($request->all());
                $message = [
                    'message' => 'user updated successfully',
                    'user' => $user
                ];
            }

        } else {
            $message = ['message' => 'you don\'t have the role'];
        }
        return response()->json($message);
    }

    public function editemployee(Request $request)
    {
        $data = $request->validate([
            'id' => ['required'],
            'fname' => ['required', 'string'],
            'lname' => ['required', 'string'],
            'birthday' => ['required', 'string'],
            'gendere' => ['required', 'string'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'nationality' => ['required', 'string'],
            'national_id' => ['required', 'numeric', 'unique:users,national_id'],
            'notes' => ['required', 'string']
        ]);
        if (!$data) {
            $message = ['message' => 'incorrect required '];
        } else {
            $user = User::where('id', $request->id)->first();
            if (!$user) {
                $message = ['message' => 'incorrect id '];
            } else {
                $user_type = $user->user_type;
                if ($user_type == 'secertary' || $user_type == 'doctor') {

                    $data['birthday']=Carbon::parse($data['birthday'])->format('Y-m-d');
                    $user->update($request->all());
                    $message = [
                        'message' => 'user updated successfully',
                        'user' => $user
                    ];
                } else {
                    $message = ['message' => 'you don\'t have the role'];
                }
            }
        }
        return response()->json($message);
    }

    public function changepassword(Request $request)
    {
        $user_id = auth()->user()->id;
        $user = User::where('id', $user_id)->first();
        $data = $request->validate([
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string'],
        ]);
        if (!$data) {
            $message = ['message' => 'incorrect required'];
        } else {
            if (!Hash::check($request->old_password, $user->password)) {
                $message = ['message' => 'password incorrect'];
            } else {
                $user['password'] = $request->new_password;
                $user->update();
                $message = ['message' => 'password changeing successfully'];
            }
        }
        return response()->json($message);
    }
}
