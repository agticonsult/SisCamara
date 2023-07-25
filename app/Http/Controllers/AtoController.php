<?php

namespace App\Http\Controllers;

use App\Models\Ato;
use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AtoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $atos = Ato::where('ativo', '=', 1)->get();

            return view('ato.index', compact('atos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Ato $ato)
    {
        //
    }

    public function edit(Ato $ato)
    {
        //
    }

    public function update(Request $request, Ato $ato)
    {
        //
    }

    public function destroy(Ato $ato)
    {
        //
    }
}
