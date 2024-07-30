<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        dd(auth()->user());
        return view("chat.index");
    }
}
