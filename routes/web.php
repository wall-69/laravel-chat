<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(HomeController::class)->group(function () {
    Route::get("/", "index")->name("index");

    Route::middleware(["guest"])->group(function () {
        Route::get("/login", "login")->name("login");
        Route::get("/register", "register")->name("register");
    });
});

Route::controller(UserController::class)->group(function () {
    Route::middleware(["guest"])->group(function () {
        Route::post("/login", "login")->name("user.login");
        Route::post("/register", "register")->name("user.register");
    });
});

Route::controller(ChatController::class)->group(function () {
    Route::middleware(["auth"])->group(function () {
        Route::get("/chat", "index")->name("chat.index");
    });
});
