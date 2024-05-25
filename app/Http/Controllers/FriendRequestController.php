<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FriendRequestController extends Controller
{
    public function sendRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $friendRequest = new FriendRequest();
        $friendRequest->sender_id = Auth::id();
        $friendRequest->receiver_id = $request->receiver_id;
        $friendRequest->save();

        return response()->json(['message' => 'Friend request sent successfully']);
    }

    public function getRequests()
    {
        $requests = FriendRequest::where('receiver_id', Auth::id())->where('status', 'pending')->get();
        return response()->json($requests);
    }

    public function cancelRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $friendRequest = FriendRequest::where('sender_id', Auth::id())
            ->where('receiver_id', $request->receiver_id)
            ->where('status', 'pending')
            ->first();

        if ($friendRequest) {
            $friendRequest->delete();
            return response()->json(['message' => 'Friend request canceled successfully']);
        }

        return response()->json(['message' => 'Friend request not found'], 404);
    }

    public function acceptRequest(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:users,id',
        ]);

        $friendRequest = FriendRequest::where('sender_id', $request->sender_id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($friendRequest) {
            $friendRequest->status = 'accepted';
            $friendRequest->save();
            return response()->json(['message' => 'Friend request accepted successfully']);
        }

        return response()->json(['message' => 'Friend request not found'], 404);
    }

    public function rejectRequest(Request $request)
    {
        $friendRequest = FriendRequest::where('sender_id', $request->sender_id)
                ->where('receiver_id', Auth::id())
                ->where('status', 'pending')
                ->first();

        if ($friendRequest) {
            $friendRequest->delete();
            return response()->json(['message' => 'Friend request rejected successfully']);
        }

        return response()->json(['error' => 'No pending friend request found for rejection'], 404);
    }
}

