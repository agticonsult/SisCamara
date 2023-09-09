<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\ModeloProposicao;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ModeloProposicaoController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelos = ModeloProposicao::where('ativo', '=', 1)->get();

            return view('proposicao.modelo.index', compact('modelos'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ModeloProposicaoController";
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
            if(Auth::user()->temPermissao('ModeloProposicao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            return view('proposicao.modelo.create');
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ModeloProposicaoController";
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
            if(Auth::user()->temPermissao('ModeloProposicao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'assunto' => $request->assunto,
                'conteudo' => $request->conteudo,
            ];
            $rules = [
                'assunto' => 'required',
                'conteudo' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $modelo_proposicao = new ModeloProposicao();
            $modelo_proposicao->assunto = $request->assunto;
            $modelo_proposicao->conteudo = $request->conteudo;
            $modelo_proposicao->cadastradoPorUsuario = Auth::user()->id;
            $modelo_proposicao->ativo = 1;
            $modelo_proposicao->save();

            return redirect()->route('proposicao.modelo.index')->with('success', 'Cadastro realizado com sucesso');
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
            $erro->controlador = "ModeloProposicaoController";
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
            if(Auth::user()->temPermissao('ModeloProposicao', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$modelo_proposicao){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            return view('proposicao.modelo.edit', compact('modelo_proposicao'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ModeloProposicaoController";
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
            if(Auth::user()->temPermissao('ModeloProposicao', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'id' => $id,
                'assunto' => $request->assunto,
                'conteudo' => $request->conteudo,
            ];
            $rules = [
                'id' => 'required|integer',
                'assunto' => 'required',
                'conteudo' => 'required',
            ];

            $validar = Validator::make($input, $rules);
            $validar->validate();

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$modelo_proposicao){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            $modelo_proposicao->assunto = $request->assunto;
            $modelo_proposicao->conteudo = $request->conteudo;
            $modelo_proposicao->save();

            return redirect()->route('proposicao.modelo.index')->with('success', 'Alteração realizada com sucesso');
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
            $erro->controlador = "ModeloProposicaoController";
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
            if (Auth::user()->temPermissao('ModeloProposicao', 'Exclusão') != 1) {
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

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$modelo_proposicao){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            $modelo_proposicao->inativadoPorUsuario = Auth::user()->id;
            $modelo_proposicao->dataInativado = Carbon::now();
            $modelo_proposicao->motivoInativado = $motivo;
            $modelo_proposicao->ativo = 0;
            $modelo_proposicao->save();

            return redirect()->route('proposicao.modelo.index')->with('success', 'Exclusão realizada com sucesso.');
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
            $erro->controlador = "AtividadeLazerController";
            $erro->funcao = "destroy";
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function get($id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$modelo_proposicao){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            return $this->success($modelo_proposicao);
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "ModeloProposicaoController";
            $erro->funcao = "get";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return $this->error('Erro, contate o administrador do sistema', 500);
        }
    }
}
