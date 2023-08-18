<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use App\Models\PublicacaoAto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PublicacaoAtoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('PublicacaoAto', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $publicacaos = PublicacaoAto::where('ativo', '=', 1)->get();

            return view('configuracao.publicacao-ato.index', compact('publicacaos'));
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "PublicacaoAtoController";
            $erro->funcao = "index";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if(Auth::user()->temPermissao('PublicacaoAto', 'Cadastro') != 1){
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

            $publicacao = new PublicacaoAto();
            $publicacao->descricao = $request->descricao;
            $publicacao->cadastradoPorUsuario = Auth::user()->id;
            $publicacao->ativo = 1;
            $publicacao->save();

            return redirect()->route('configuracao.publicacao_ato.index')->with('success', 'Cadastro realizado com sucesso.');

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
            $erro->controlador = "PublicacaoAtoController";
            $erro->funcao = "store";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('PublicacaoAto', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $publicacao = PublicacaoAto::where('id', '=', $id)->where('ativo', '=', 1)->first();

            if (!$publicacao){
                return redirect()->route('configuracao.publicacao_ato.index')->with('erro', 'Não é possível alterar esta publicacao.');
            }

            return view('configuracao.publicacao-ato.edit', compact('publicacao'));
        }
        catch(\Exception $ex){
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "PublicacaoAtoController";
            $erro->funcao = "edit";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('PublicacaoAto', 'Alteração') != 1){
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

            $publicacao = PublicacaoAto::find($id);
            $publicacao->descricao = $request->descricao;
            $publicacao->save();

            return redirect()->route('configuracao.publicacao_ato.index')->with('success', 'Alteração realizada com sucesso.');

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
            $erro->controlador = "PublicacaoAtoController";
            $erro->funcao = "update";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('PublicacaoAto', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $input = [
                'motivo' => $request->motivo
            ];
            $rules = [
                'motivo' => 'max:255'
            ];

            $validarUsuario = Validator::make($input, $rules);
            $validarUsuario->validate();

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $publicacao = PublicacaoAto::where('id', '=', $id)->where('ativo', '=', 1)->first();

            if (!$publicacao){
                return redirect()->back()->with('erro', 'Não é possível excluir esta publicação.')->withInput();
            }

            $publicacao->inativadoPorUsuario = Auth::user()->id;
            $publicacao->dataInativado = Carbon::now();
            $publicacao->motivoInativado = $motivo;
            $publicacao->ativo = 0;
            $publicacao->save();

            return redirect()->route('configuracao.publicacao_ato.index')->with('success', 'Exclusão realizada com sucesso.');
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
            $erro->controlador = "PublicacaoAtoController";
            $erro->funcao = "destroy";
            if (Auth::check()) {
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
