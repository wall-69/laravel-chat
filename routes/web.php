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
Route::controller(ChatController::class)->name("chat.")->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get("/chat", "index")->name("index");
        Route::get("/chat/create", "create")->name("create");
        Route::get("/channels", "channels")->name("channels");

        Route::post("/chat", "store")->name("store");
        // api perhaps?
        Route::post("/chats/{chat}/join", "join")->name("join");
        Route::post("/chats/{chat}/leave", "leave")->name("leave");
        Route::post("/chats/{chat}/kick", "kick")->name("kick");

        // Should be API???
        Route::patch("/chats/{chat}", "update")->name("update");
        Route::delete("/chats/{chat}", "destroy")->name("destroy");
    });
});

// ChatAdmin
// SHOULD BE API???
Route::controller(ChatAdminController::class)->name("chatAdmins.")->middleware("auth")->group(function () {
    Route::post("/chats/{chat}/change-admin", "changeAdmin")->name("changeAdmin");
});

// UserChat
Route::controller(UserChatController::class)->name("userChats.")->middleware("auth")->group(function () {
    Route::post("/user-chats/{userChat}/last-read", "updateLastRead")->name("updateLastRead");
});

// UserBlock
Route::controller(UserBlockController::class)->name("userBlocks.")->middleware("auth")->group(function () {
    Route::post("/user-blocks", "store")->name("store");
    Route::delete("/user-blocks/{userBlock}", "destroy")->name("destroy");
});

// UserChatBan
// TODO: API?????
Route::controller(UserChatBanController::class)->name("userChatBans.")->middleware("auth")->group(function () {
    Route::post("/user-chat-bans/", "store")->name("store");
    Route::delete("/user-chat-bans/{userChatBan}", "destroy")->name("destroy");
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
