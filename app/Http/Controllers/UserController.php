<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        if (auth()->attempt($data)) {
            $request->session()->regenerateToken();
            $request->session()->regenerate();

            return redirect(route("chat.index"));
        }

        return redirect(route("login"))->withErrors(["email" => "The provided credentials do not match our records."]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            "nickname" => "required|min:5|max:20",
            "email" => "required|email",
            "password" => "required|min:5"
        ]);

        $user = User::create($data);
        auth()->login($user);

        return redirect(route("chat.index"));
    }
}
