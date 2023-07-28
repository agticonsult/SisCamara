<?php

namespace App\Http\Controllers;

use App\Models\Ato;
use App\Models\ErrorLog;
use App\Models\Grupo;
use App\Models\TipoAto;
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
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $grupos = Grupo::where('ativo', '=', 1)->get();
            $tipo_atos = TipoAto::where('ativo', '=', 1)->get();

            return view('ato.create', compact('grupos', 'tipo_atos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
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
        try {
            if(Auth::user()->temPermissao('Ato', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $corpo_texto = $request->corpo_texto;
            $corpo_texto_alterado = preg_replace('/\r/', '', $corpo_texto);
            $array_corpo_texto = explode("\n", $corpo_texto_alterado);
            $array_corpo_texto_finalizado = [];
            for ($i = 0; $i < Count($array_corpo_texto); $i++){
                if ($array_corpo_texto[$i] != ""){
                    array_push($array_corpo_texto_finalizado, $array_corpo_texto[$i]);
                }
            }
            dd($corpo_texto, $corpo_texto_alterado, $array_corpo_texto, $array_corpo_texto_finalizado);

            return view('ato.create');
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "AtoController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
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
