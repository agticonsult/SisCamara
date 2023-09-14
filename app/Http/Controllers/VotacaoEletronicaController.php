<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\VotacaoEletronica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VotacaoEletronicaController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacaos = VotacaoEletronica::where('ativo', '=', 1)->get();

            return view('votacao-eletronica.index', compact('votacaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VotacaoEletronicaController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    // public function create()
    // {
    //     try {
    //         if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
    //             return redirect()->back()->with('erro', 'Acesso negado.');
    //         }

    //         return view('votacao-eletronica.create');
    //     }
    //     catch (\Exception $ex) {
    //         $erro = new ErrorLog();
    //         $erro->erro = $ex->getMessage();
    //         $erro->controlador = "VotacaoEletronicaController";
    //         $erro->funcao = "create";
    //         if (Auth::check()){
    //             $erro->cadastradoPorUsuario = auth()->user()->id;
    //         }
    //         $erro->save();
    //         return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
    //     }
    // }

    // public function store(Request $request)
    // {
    //     try {
    //         if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
    //             return redirect()->back()->with('erro', 'Acesso negado.');
    //         }

    //         $input = [
    //             'assunto' => $request->assunto,
    //             'conteudo' => $request->conteudo,
    //         ];
    //         $rules = [
    //             'assunto' => 'required',
    //             'conteudo' => 'required',
    //         ];

    //         $validar = Validator::make($input, $rules);
    //         $validar->validate();

    //         $modelo_documento = new ModeloDocumento();
    //         $modelo_documento->assunto = $request->assunto;
    //         $modelo_documento->conteudo = $request->conteudo;
    //         $modelo_documento->cadastradoPorUsuario = Auth::user()->id;
    //         $modelo_documento->ativo = 1;
    //         $modelo_documento->save();

    //         return redirect()->route('votacao-eletronica.index')->with('success', 'Cadastro realizado com sucesso');
    //     }
    //     catch (ValidationException $e ) {
    //         $message = $e->errors();
    //         return redirect()->back()
    //             ->withErrors($message)
    //             ->withInput();
    //     }
    //     catch (\Exception $ex) {
    //         $erro = new ErrorLog();
    //         $erro->erro = $ex->getMessage();
    //         $erro->controlador = "VotacaoEletronicaController";
    //         $erro->funcao = "store";
    //         if (Auth::check()){
    //             $erro->cadastradoPorUsuario = auth()->user()->id;
    //         }
    //         $erro->save();
    //         return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
    //     }
    // }

    // public function edit($id)
    // {
    //     try {
    //         if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
    //             return redirect()->back()->with('erro', 'Acesso negado.');
    //         }

    //         $modelo_documento = ModeloDocumento::where('id', '=', $id)->where('ativo', '=', 1)->first();
    //         if (!$modelo_documento){
    //             return redirect()->back()->with('erro', 'Modelo inválido.');
    //         }

    //         return view('votacao-eletronica.edit', compact('modelo_documento'));
    //     }
    //     catch (\Exception $ex) {
    //         $erro = new ErrorLog();
    //         $erro->erro = $ex->getMessage();
    //         $erro->controlador = "VotacaoEletronicaController";
    //         $erro->funcao = "edit";
    //         if (Auth::check()){
    //             $erro->cadastradoPorUsuario = auth()->user()->id;
    //         }
    //         $erro->save();
    //         return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
    //     }
    // }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
    //             return redirect()->back()->with('erro', 'Acesso negado.');
    //         }

    //         $input = [
    //             'id' => $id,
    //             'assunto' => $request->assunto,
    //             'conteudo' => $request->conteudo,
    //         ];
    //         $rules = [
    //             'id' => 'required|integer',
    //             'assunto' => 'required',
    //             'conteudo' => 'required',
    //         ];

    //         $validar = Validator::make($input, $rules);
    //         $validar->validate();

    //         $modelo_documento = ModeloDocumento::where('id', '=', $id)->where('ativo', '=', 1)->first();
    //         if (!$modelo_documento){
    //             return redirect()->back()->with('erro', 'Modelo inválido.');
    //         }

    //         $modelo_documento->assunto = $request->assunto;
    //         $modelo_documento->conteudo = $request->conteudo;
    //         $modelo_documento->save();

    //         return redirect()->route('votacao-eletronica.index')->with('success', 'Alteração realizada com sucesso');
    //     }
    //     catch (ValidationException $e ) {
    //         $message = $e->errors();
    //         return redirect()->back()
    //             ->withErrors($message)
    //             ->withInput();
    //     }
    //     catch (\Exception $ex) {
    //         $erro = new ErrorLog();
    //         $erro->erro = $ex->getMessage();
    //         $erro->controlador = "VotacaoEletronicaController";
    //         $erro->funcao = "update";
    //         if (Auth::check()){
    //             $erro->cadastradoPorUsuario = auth()->user()->id;
    //         }
    //         $erro->save();
    //         return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
    //     }
    // }

    // public function destroy(Request $request, $id)
    // {
    //     try {
    //         if (Auth::user()->temPermissao('VotacaoEletronica', 'Exclusão') != 1) {
    //             return redirect()->back()->with('erro', 'Acesso negado.');
    //         }

    //         $input = [
    //             'motivo' => $request->motivo
    //         ];
    //         $rules = [
    //             'motivo' => 'max:255'
    //         ];

    //         $validar = Validator::make($input, $rules);
    //         $validar->validate();

    //         $motivo = $request->motivo;

    //         if ($request->motivo == null || $request->motivo == "") {
    //             $motivo = "Exclusão pelo usuário.";
    //         }

    //         $modelo_documento = ModeloDocumento::where('id', '=', $id)->where('ativo', '=', 1)->first();
    //         if (!$modelo_documento){
    //             return redirect()->back()->with('erro', 'Modelo inválido.');
    //         }

    //         $modelo_documento->inativadoPorUsuario = Auth::user()->id;
    //         $modelo_documento->dataInativado = Carbon::now();
    //         $modelo_documento->motivoInativado = $motivo;
    //         $modelo_documento->ativo = 0;
    //         $modelo_documento->save();

    //         return redirect()->route('votacao-eletronica.index')->with('success', 'Exclusão realizada com sucesso.');
    //     }
    //     catch (ValidationException $e) {
    //         $message = $e->errors();
    //         return redirect()->back()
    //             ->withErrors($message)
    //             ->withInput();
    //     }
    //     catch (\Exception $ex) {
    //         $erro = new ErrorLog();
    //         $erro->erro = $ex->getMessage();
    //         $erro->controlador = "AtividadeLazerController";
    //         $erro->funcao = "destroy";
    //         if (Auth::check()) {
    //             $erro->cadastradoPorUsuario = auth()->user()->id;
    //         }
    //         $erro->save();
    //         return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
    //     }
    // }

}
