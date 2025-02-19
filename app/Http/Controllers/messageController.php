<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class messageController extends Controller
{
    //
    public function deleteMessage(Request $request)
    {
        $data = $request->validate([
            'id' => ['required'],
        ]);
        if (!$data) {
            $message = ['message' => 'id required'];
        } else {
            $_message = Message::where('id', $request->id)->first();
            if (!$_message) {
                $message = ['message' => 'incourrect id'];
            } else {
                $token_id = auth()->user()->id;
                if ($token_id == $_message->user_id) {
                    $_message->delete();
                } else {
                    $message = ['message' => 'you don\'t have the role'];
                }
            }
        }
        return response()->json($message);
    }
}
