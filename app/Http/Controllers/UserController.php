<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tries to authenticate the guest with specified email and password.
     */
    public function login(Request $request)
    {
        // Validate
        $data = $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // Try to authenticate with specified data
        if (auth()->attempt($data)) {
            // Regenerate CSRF token and session id for security or smth
            $request->session()->regenerateToken();
            $request->session()->regenerate();

            return redirect(route("chat.index"));
        }

        // Redirect to login page with error
        return redirect(route("login"))->withErrors(["email" => "The provided credentials do not match our records."]);
    }

    /**
     * Stores the user to database with specified nickname, email, password and profile picture.
     */
    public function store(Request $request)
    {
        // Validate
        $data = $request->validate([
            "nickname" => "required|min:5|max:20",
            "email" => "required|email|unique:users",
            "password" => "required|min:5",
            "profile_picture" => "required|image"
        ]);

        // Profile picture
        $filePath = $request->profile_picture->store("img/pfp", "public");
        $data["profile_picture"] = "storage/" . $filePath;

        // Create model and login
        $user = User::create($data);
        auth()->login($user);

        return redirect(route("chat.index"));
    }

    /**
     * Updates the user with specified data.
     */
    public function update(Request $request, User $user)
    {
        // Check, if the user updated is the user authenticated, just to make sure
        if ($user->id != auth()->user()->id) {
            abort(403);
        }

        $data = [];

        // Nickname
        if ($request->filled("nickname")) {
            $request->validate([
                "nickname" => "min:5|max:20",
            ]);

            $data["nickname"] = $request->nickname;
        }
        // Profile picture 
        if ($request->hasFile("profile_picture")) {
            $request->validate([
                "profile_picture" => "image"
            ]);

            $filePath = $request->profile_picture->store("img/pfp", "public");
            $data["profile_picture"] = "storage/" . $filePath;
        }
        // Email
        if ($request->filled("email")) {
            $request->validate([
                "email" => "email|unique:users",
                "password" => "required",
            ]);

            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors(["password" => "The password is incorrect."]);
            }

            $data["email"] = $request->email;
        }
        // Password
        if ($request->filled("new_password")) {
            $request->validate([
                "new_password" => "min:5",
                "password" => "required",
            ]);

            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors(["change_password" => "The password is incorrect."]);
            }

            $data["password"] = Hash::make($request->new_password);
        }

        $user->update($data);

        return redirect(route("users.show", ["nickname" => $user->nickname]));
    }

    /**
     * Logs the user out.
     */
    public function logout(Request $request)
    {
        // Log out the user
        auth()->logout();

        // Invalidate session id and regenerate CSRF token for security or smth
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route("index"));
    }

    /**
     * Shows user profile page based on the nickname specified.
     */
    public function show(string $nickname)
    {
        // Get the user by his nickname.
        $user = User::where("nickname", $nickname)->first();

        return view("chat.profile", ["user" => $user]);
    }
}
