<?php

namespace App\Http\Controllers;

class InicioController extends Controller
{

    function index()
    {
        return view('inicio');
    }

    public function login()
    {
        return view('auth.login');
    }
}
