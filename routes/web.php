<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FriendRequestController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () { return view('auth.login'); })->name('login.view');
    Route::get('/register', function () { return view('auth.register'); })->name('register.view');
});

Route::post('/logout',[AuthController::class,'logout'])->name('logout');

Route::post('/login-post',[AuthController::class,'login'])->name('login.post');
Route::post('/register-post',[AuthController::class,'register'])->name('register.post');


Route::get('/users/data', [UserController::class, 'getData'])->name('users.data');
Route::get('/dashboard',[UserController::class,'index'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::post('/friend-request/send', [FriendRequestController::class, 'sendRequest'])->name('friend-request.send');
    Route::post('/friend-request/cancel', [FriendRequestController::class, 'cancelRequest'])->name('friend-request.cancel');
    Route::post('/friend-request/accept', [FriendRequestController::class, 'acceptRequest'])->name('friend-request.accept');
    Route::post('/friend-request/reject', [FriendRequestController::class, 'rejectRequest'])->name('friend-request.reject');
    Route::get('/chat/{user_id}', [MessageController::class, 'index'])->name('chat');


    Route::get('/friend-requests', [FriendRequestController::class, 'getRequests'])->name('friend-requests');

    Route::post('/message/send', [MessageController::class, 'sendMessage'])->name('message.send');
    Route::get('/messages/{receiver_id}', [MessageController::class, 'getMessages'])->name('messages.get');
});
