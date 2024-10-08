<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserBlockController;
use App\Http\Controllers\UserChatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Home
Route::controller(HomeController::class)->group(function () {
    Route::get("/", "index")->name("index");

    Route::middleware("guest")->group(function () {
        Route::get("/login", "login")->name("login");
        Route::get("/register", "register")->name("register");
    });
});

// User
Route::controller(UserController::class)->name("users.")->group(function () {
    Route::get("/profile/{nickname}", "show")->name("show");

    Route::middleware("guest")->group(function () {
        Route::post("/login", "login")->name("login");
        Route::post("/register", "create")->name("create");
    });

    Route::middleware("auth")->group(function () {
        Route::post("/logout", "logout")->name("logout");
    });
});

// Chat
Route::controller(ChatController::class)->name("chat.")->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get("/chat", "index")->name("index");

        Route::post("/chat", "store")->name("store");
    });
});

// UserChat
Route::controller(UserChatController::class)->name("userChats.")->middleware("auth")->group(function () {
    Route::post("/user-chat/{userChat}/last-read", "updateLastRead")->name("updateLastRead");
});

// UserBlock
Route::controller(UserBlockController::class)->name("userBlocks.")->middleware("auth")->group(function () {
    Route::post("/user-block", "store")->name("store");
    Route::delete("/user-block/{userBlock}", "destroy")->name("destroy");
});

// Message
// TODO: SHOULD BE AN API?
Route::controller(MessageController::class)->name("messages.")->middleware("auth")->group(function () {
    Route::get("/messages/{chat}", "index")->name("index");

    Route::post("/messages/{chat}", "store")->name("store");
});

// Fallback
Route::fallback(function () {
    return redirect(route("index"));
});
