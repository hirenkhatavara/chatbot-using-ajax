<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required|min:8|max:16',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')->withSuccess('You have Successfully loggedin');
        }

        return redirect()->back()->with('error', 'Oops! You have entered invalid credentials.');
    }

    public function register(Request $request)
    {
        $this->validate($request,
        [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users',
            'phone_number' => 'required|unique:users,phone_number',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,JPG,JPEG,PNG,SVG|max:2048',
            'password' => 'required|max:16|min:8|confirmed',
        ]);

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Auth::login($user);

        return redirect()->intended('dashboard')->withSuccess('You have Successfully registered and logged in');
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect("/");
    }
}
