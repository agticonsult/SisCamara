<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\FinalidadeGrupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FinalidadeGrupoController extends Controller
{

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('FinalidadeGrupo', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $finalidades = FinalidadeGrupo::where('ativo', '=', 1)->get();

            return view('configuracao.finalidade-grupo.index', compact('finalidades'));
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "FinalidadeGrupoController";
            $erro->funcao = "index";
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
            if(Auth::user()->temPermissao('FinalidadeGrupo', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'descricao' => $request->descricao
            ];
            $rules = [
                'descricao' => 'required|max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $finalidadeGrupo = new FinalidadeGrupo();
            $finalidadeGrupo->descricao = $request->descricao;
            $finalidadeGrupo->cadastradoPorUsuario = Auth::user()->id;
            $finalidadeGrupo->ativo = 1;
            $finalidadeGrupo->save();

            return redirect()->route('configuracao.finalidade_grupo.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "FinalidadeGrupoController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('FinalidadeGrupo', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $finalidade = FinalidadeGrupo::where('id', '=', $id)->where('ativo', '=', 1)->first();

            if (!$finalidade){
                return redirect()->route('configuracao.finalidade_grupo.index')->with('erro', 'Não é possível alterar esta finalidade.');
            }

            return view('configuracao.finalidade-grupo.edit', compact('finalidade'));
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "FinalidadeGrupoController";
            $erro->funcao = "edit";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('FinalidadeGrupo', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'descricao' => $request->descricao
            ];
            $rules = [
                'descricao' => 'required|max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $finalidadeGrupo = FinalidadeGrupo::find($id);
            $finalidadeGrupo->descricao = $request->descricao;
            $finalidadeGrupo->save();

            return redirect()->route('configuracao.finalidade_grupo.index')->with('success', 'Alteração realizada com sucesso.');

        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "FinalidadeGrupoController";
            $erro->funcao = "update";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
