<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\Proposicao;
use App\Models\TipoVotacao;
use App\Models\VotacaoEletronica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $proposicaos = Proposicao::where('ativo', '=', 1)->get();
            $tipo_votacaos = TipoVotacao::where('ativo', '=', 1)->get();

            return view('votacao-eletronica.create', compact('proposicaos', 'tipo_votacaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VotacaoEletronicaController";
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
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'data' => $request->data,
                'id_tipo_votacao' => $request->id_tipo_votacao,
                'id_proposicao' => $request->id_proposicao
            ];
            $rules = [
                'data' => 'required|date',
                'id_tipo_votacao' => 'required|integer',
                'id_proposicao' => 'required|integer',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $tipo_votacao = TipoVotacao::where('id', '=', $request->id_tipo_votacao)->where('ativo', '=', 1)->first();
            if (!$tipo_votacao){
                return redirect()->back()->with('erro', 'Tipo de votação inválido!');
            }

            $proposicao = Proposicao::where('id', '=', $request->id_proposicao)->where('ativo', '=', 1)->first();
            if (!$proposicao){
                return redirect()->back()->with('erro', 'Proposição inválida!');
            }

            $votacao = new VotacaoEletronica();
            $votacao->data = $request->data;
            $votacao->id_tipo_votacao = $request->id_tipo_votacao;
            $votacao->id_proposicao = $request->id_proposicao;
            $votacao->cadastradoPorUsuario = Auth::user()->id;
            $votacao->ativo = 1;
            $votacao->save();

            return redirect()->route('votacao_eletronica.index')->with('success', 'Cadastro realizado com sucesso');
        }
        catch (ValidationException $e ) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VotacaoEletronicaController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

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

    //         return redirect()->route('votacao_eletronica.index')->with('success', 'Alteração realizada com sucesso');
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

    //         return redirect()->route('votacao_eletronica.index')->with('success', 'Exclusão realizada com sucesso.');
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
