<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\Reparticao;
use App\Models\TipoReparticao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReparticaoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $reparticaos = Reparticao::where('ativo', '=', 1)->get();

            return view('reparticao.index', compact('reparticaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ReparticaoController";
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
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipo_reparticaos = TipoReparticao::where('ativo', '=', 1)->get();

            return view('reparticao.create', compact('tipo_reparticaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ReparticaoController";
            $erro->funcao = "create";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Reparticao $reparticao)
    {
        //
    }

    public function edit(Reparticao $reparticao)
    {
        //
    }

    public function update(Request $request, Reparticao $reparticao)
    {
        //
    }

    public function destroy(Reparticao $reparticao)
    {
        //
    }
}
