<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Shows the index page.
     */
    public function index()
    {
        return view("index");
    }

    /**
     * Shows the login page.
     */
    public function login()
    {
        return view("login");
    }

    /**
     * Shows the register page.
     */
    public function register()
    {
        return view("register");
    }
}
