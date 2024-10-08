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

    public function store(Request $request)
    {
        $data = $request->validate([
            "nickname" => "required|min:5|max:20",
            "email" => "required|email",
            "password" => "required|min:5",
            "profile_picture" => "required|image"
        ]);

        // Profile picture
        $filePath = $request->file("profile_picture")->store("img/pfp", "public");
        $data["profile_picture"] = "storage/" . $filePath;

        $user = User::create($data);
        auth()->login($user);

        return redirect(route("chat.index"));
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route("index"));
    }

    public function show(string $nickname)
    {
        $user = User::where("nickname", $nickname)->first();
        return view("chat.profile", ["user" => $user]);
    }
}
