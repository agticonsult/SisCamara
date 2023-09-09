<?php

namespace App\Http\Controllers;

use App\Models\Proposicao;
use App\Models\ErrorLog;
use App\Models\ModeloProposicao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProposicaoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Proposicao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $proposicaos = Proposicao::where('ativo', '=', 1)->get();

            return view('proposicao.index', compact('proposicaos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ProposicaoController";
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
            if(Auth::user()->temPermissao('Proposicao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelos = ModeloProposicao::where('ativo', '=', 1)->get();

            return view('proposicao.create', compact('modelos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ProposicaoController";
            $erro->funcao = "create";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function show($id){

    }

    public function store(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('Proposicao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'titulo' => $request->titulo,
                'id_modelo' => $request->id_modelo,
                'assunto' => $request->assunto,
                'conteudo' => $request->conteudo,
            ];
            $rules = [
                'titulo' => 'required',
                'id_modelo' => 'required|integer',
                'assunto' => 'required',
                'conteudo' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $modelo = ModeloProposicao::where('id', '=', $request->id_modelo)->where('ativo', '=', 1)->first();
            if (!$modelo){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            $proposicao = new Proposicao();
            $proposicao->titulo = $request->titulo;
            $proposicao->id_modelo = $request->id_modelo;
            $proposicao->assunto = $request->assunto;
            $proposicao->conteudo = $request->conteudo;
            $proposicao->id_status = 1;
            $proposicao->id_localizacao = 1;
            $proposicao->cadastradoPorUsuario = Auth::user()->id;
            $proposicao->ativo = 1;
            $proposicao->save();

            return redirect()->route('proposicao.index')->with('success', 'Cadastro realizado com sucesso');
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
            $erro->controlador = "ProposicaoController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Proposicao', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $proposicao = Proposicao::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$proposicao){
                return redirect()->back()->with('erro', 'Proposicao inválido.');
            }

            $modelos = ModeloProposicao::where('ativo', '=', 1)->get();

            return view('proposicao.edit', compact('proposicao', 'modelos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ProposicaoController";
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
            if(Auth::user()->temPermissao('Proposicao', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $id,
                'titulo' => $request->titulo,
                'assunto' => $request->assunto,
                'conteudo' => $request->conteudo,
            ];
            $rules = [
                'id' => 'required|integer',
                'titulo' => 'required',
                'assunto' => 'required',
                'conteudo' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $proposicao = Proposicao::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$proposicao){
                return redirect()->back()->with('erro', 'Proposicao inválido.');
            }

            $proposicao->titulo = $request->titulo;
            $proposicao->assunto = $request->assunto;
            $proposicao->conteudo = $request->conteudo;
            $proposicao->save();

            return redirect()->route('proposicao.index')->with('success', 'Alteração realizada com sucesso');
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
            $erro->controlador = "ProposicaoController";
            $erro->funcao = "update";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Proposicao', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'motivo' => $request->motivo
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $proposicao = Proposicao::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$proposicao){
                return redirect()->back()->with('erro', 'Proposicao inválido.');
            }

            $proposicao->inativadoPorUsuario = Auth::user()->id;
            $proposicao->dataInativado = Carbon::now();
            $proposicao->motivoInativado = $motivo;
            $proposicao->ativo = 0;
            $proposicao->save();

            return redirect()->route('proposicao.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (ValidationException $e) {
            $message = $e->errors();
            return redirect()->back()
                ->withErrors($message)
                ->withInput();
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ProposicaoController";
            $erro->funcao = "destroy";
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
