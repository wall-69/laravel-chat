<?php

use App\Http\Controllers\ChatAdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserChatBanController;
use App\Http\Controllers\UserChatController;
use App\Http\Controllers\UserController;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Chat
Route::controller(ChatController::class)->name("chats.")->group(function () {
    Route::middleware(["auth", "web"])->group(function () {
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
Route::controller(ChatAdminController::class)->name("chatAdmins.")->middleware(["auth", "web"])->group(function () {
    Route::post("/chats/{chat}/change-admin", "changeAdmin")->name("changeAdmin");
});

// UserChat
Route::controller(UserChatController::class)->name("userChats.")->middleware(["auth", "web"])->group(function () {
    Route::post("/user-chats/{userChat}/last-read", "updateLastRead")->name("updateLastRead");
});

// UserChatBan
Route::controller(UserChatBanController::class)->name("userChatBans.")->middleware(["auth", "web"])->group(function () {
    Route::post("/user-chat-bans/", "store")->name("store");
    Route::delete("/user-chat-bans/{userChatBan}", "destroy")->name("destroy");
});


// Message
Route::controller(MessageController::class)->name("messages.")->middleware(["auth", "web"])->group(function () {
    Route::get("/messages/{chat}", "index")->name("index");

    Route::post("/messages/{chat}", "store")->name("store");
});
