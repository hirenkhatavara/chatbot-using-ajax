<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    // public function getData()
    // {
    //     $authUserId = Auth::id();
    //     $users = User::select(['id', 'first_name', 'last_name', 'email', 'phone_number', 'profile_picture', 'created_at'])
    //         ->with([
    //             'sentFriendRequests' => function($query) use ($authUserId) {
    //                 $query->where('receiver_id', $authUserId);
    //             },
    //             'receivedFriendRequests' => function($query) use ($authUserId) {
    //                 $query->where('sender_id', $authUserId);
    //             }
    //         ])
    //         ->where('id','!=',$authUserId)
    //         ->orderBy('id','desc')
    //     ->get();

    //     return DataTables::of($users)
    //         ->addColumn('profile_picture', function ($user) {
    //             if ($user->profile_picture) {
    //                 return '<img src="'. asset('storage/' . $user->profile_picture) .'" alt="Profile Picture" width="50" height="50">';
    //             } else {
    //                 return 'No Picture';
    //             }
    //         })
    //         ->addColumn('action', function ($user) use ($authUserId) {
    //             $sentRequest = $user->sentFriendRequests->first();
    //             $receivedRequest = $user->receivedFriendRequests->first();
    //             // dd($receivedRequest);

    //             if ($sentRequest) {
    //                 if ($sentRequest->status == 'pending') {
    //                     // Display button for the sender to cancel the request
    //                     return '<button class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 cancel-request" data-id="' . $user->id . '">Cancel Request</button>';
    //                 } elseif ($sentRequest->status == 'accepted') {
    //                     // Display button to chat if the request is accepted
    //                     return '<a href="'. route('chat', ['user_id' => $user->id]) .'" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Chat</a>';
    //                 }
    //             }
    //             elseif ($receivedRequest) {
    //                 if ($receivedRequest->status == 'pending') {
    //                     // Display buttons for the receiver to accept or reject the request
    //                     return '<button class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 accept-request" data-id="' . $user->id . '">Accept Request</button>
    //                             <button class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 reject-request" data-id="' . $user->id . '">Reject Request</button>';
    //                 } elseif ($receivedRequest->status == 'accepted') {
    //                     // Display button to chat if the request is accepted
    //                     return '<a href="'. route('chat', ['user_id' => $user->id]) .'" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Chat</a>';
    //                 }
    //             }
    //             else {
    //                 // Display button for sending friend request if no request sent yet
    //                 return '<button class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900 send-request" data-id="' . $user->id . '">Send Request</button>';
    //             }
    //         })

    //         ->rawColumns(['profile_picture','action'])
    //         ->make(true);
    // }

    public function getData()
    {
        $authUserId = Auth::id();
        $users = User::select(['id', 'first_name', 'last_name', 'email', 'phone_number', 'profile_picture', 'created_at'])
            ->with([
                'receivedFriendRequests' => function($query) use ($authUserId) {
                    $query->where('sender_id', $authUserId);
                },
                'sentFriendRequests' => function($query) use ($authUserId) {
                    $query->where('receiver_id', $authUserId);
                }
            ])
            ->where('id', '!=', $authUserId)
            ->orderBy('id', 'desc')
            ->get();

        return DataTables::of($users)
            ->addColumn('profile_picture', function ($user) {
                if ($user->profile_picture) {
                    return '<img src="'. asset('storage/' . $user->profile_picture) .'" alt="Profile Picture" width="50" height="50">';
                } else {
                    return 'No Picture';
                }
            })
            ->addColumn('action', function ($user) use ($authUserId) {
                $sentRequest = $user->receivedFriendRequests->first();
                $receivedRequest = $user->sentFriendRequests->first();

                if ($sentRequest) {
                    // Authenticated user is the sender
                    if ($sentRequest->status == 'pending') {
                        return '<button class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 cancel-request" data-id="' . $user->id . '">Cancel Request</button>';
                    } elseif ($sentRequest->status == 'accepted') {
                        return '<a href="'. route('chat', ['user_id' => $user->id]) .'" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Chat</a>';
                    }
                } elseif ($receivedRequest) {
                    // Authenticated user is the receiver
                    if ($receivedRequest->status == 'pending') {
                        return '<button class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 accept-request" data-id="' . $user->id . '">Accept Request</button>
                                <button class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 reject-request" data-id="' . $user->id . '">Reject Request</button>';
                    } elseif ($receivedRequest->status == 'accepted') {
                        return '<a href="'. route('chat', ['user_id' => $user->id]) .'" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Chat</a>';
                    }
                } else {
                    return '<button class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900 send-request" data-id="' . $user->id . '">Send Request</button>';
                }
            })
            ->rawColumns(['profile_picture', 'action'])
            ->make(true);
    }
}
