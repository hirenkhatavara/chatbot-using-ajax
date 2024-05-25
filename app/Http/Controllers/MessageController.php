<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index($user_id)
    {
        $user = User::findOrFail($user_id);
        return view('chat', compact('user'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $friendship = FriendRequest::where('sender_id', Auth::id())
            ->where('receiver_id', $request->receiver_id)
            ->where('status', 'accepted')
            ->exists() || FriendRequest::where('sender_id', $request->receiver_id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'accepted')
            ->exists();

        if (!$friendship) {
            return response()->json(['message' => 'You are not friends with this user'], 403);
        }

        $message = new Message();
        $message->sender_id = Auth::id();
        $message->receiver_id = $request->receiver_id;
        $message->message = $request->message;
        $message->save();

        return response()->json(['message' => 'Message sent successfully']);
    }

    public function getMessages($receiver_id)
    {
        $messages = Message::where(function($query) use ($receiver_id) {
            $query->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
        })
        ->where(function($query) use ($receiver_id) {
            $query->where('sender_id', $receiver_id)
                  ->orWhere('receiver_id', $receiver_id);
        })
        ->with('sender:id,first_name')
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json($messages);
    }
}
