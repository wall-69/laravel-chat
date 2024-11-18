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

// All routes need authentication so do it like this lol
Route::middleware("auth:sanctum")->group(function () {
    // Chat
    Route::controller(ChatController::class)->name("chats.")->group(function () {
        Route::post("/chats/{chat}/join", "join")->name("join");
        Route::post("/chats/{chat}/leave", "leave")->name("leave");
        Route::post("/chats/{chat}/kick", "kick")->name("kick");

        Route::patch("/chats/{chat}", "update")->name("update");
        Route::delete("/chats/{chat}", "destroy")->name("destroy");
    });

    // ChatAdmin
    Route::controller(ChatAdminController::class)->name("chatAdmins.")->group(function () {
        Route::post("/chats/{chat}/change-admin", "changeAdmin")->name("changeAdmin");
    });

    // UserChat
    Route::controller(UserChatController::class)->name("userChats.")->group(function () {
        Route::post("/user-chats/{userChat}/last-read", "updateLastRead")->name("updateLastRead");
    });

    // UserChatBan
    Route::controller(UserChatBanController::class)->name("userChatBans.")->group(function () {
        Route::post("/user-chat-bans/", "store")->name("store");
        Route::delete("/user-chat-bans/{userChatBan}", "destroy")->name("destroy");
    });


    // Message
    Route::controller(MessageController::class)->name("messages.")->group(function () {
        Route::get("/messages/{chat}", "index")->name("index");

        Route::post("/messages/{chat}", "store")->name("store");
    });
});
