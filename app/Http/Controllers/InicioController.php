<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioController extends Controller
{

    function index() {
        return view('inicio');
    }

    public function login() {
        return view('auth.login');
    }
}
