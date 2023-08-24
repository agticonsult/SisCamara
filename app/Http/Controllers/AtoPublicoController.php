<?php

namespace App\Http\Controllers;

use App\Models\Ato;
use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AtoPublicoController extends Controller
{
    public function index()
    {
        try {
            $atos = Ato::where('ativo', '=', 1)->get();

            return view('ato.publico.index', compact('atos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoPublicoController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
