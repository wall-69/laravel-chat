<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->group(function () {
    Route::get("/", "index")->name("index");

    Route::middleware("guest")->group(function () {
        Route::get("/login", "login")->name("login");
        Route::get("/register", "register")->name("register");
    });
});

Route::controller(UserController::class)->name("users.")->group(function () {
    Route::middleware("guest")->group(function () {
        Route::post("/login", "login")->name("login");
        Route::post("/register", "create")->name("create");
    });

    Route::middleware("auth")->group(function () {
        Route::post("/logout", "logout")->name("logout");
    });
});

Route::controller(ChatController::class)->group(function () {
    Route::middleware("auth")->group(function () {
        Route::get("/chat", "index")->name("chat.index");
    });
});
