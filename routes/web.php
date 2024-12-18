<?php

use App\Http\Controllers\ChatAdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserBlockController;
use App\Http\Controllers\UserChatBanController;
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
        Route::post("/register", "store")->name("store");
    });

    Route::middleware("auth")->group(function () {
        Route::post("/logout", "logout")->name("logout");

        Route::patch("/users/{user}", "update")->name("update");
    });
});

// Chat
Route::controller(ChatController::class)->name("chats.")->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get("/chat", "index")->name("index");
        Route::get("/chat/create", "create")->name("create");
        Route::get("/channels", "channels")->name("channels");

        Route::post("/chat", "store")->name("store");
    });
});

// UserBlock
Route::controller(UserBlockController::class)->name("userBlocks.")->middleware("auth")->group(function () {
    Route::post("/user-blocks", "store")->name("store");
    Route::delete("/user-blocks/{userBlock}", "destroy")->name("destroy");
});

// Fallback
Route::fallback(function () {
    return redirect(route("index"));
});
