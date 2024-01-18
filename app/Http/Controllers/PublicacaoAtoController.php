<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicacaoAtoRequest;
use App\Models\ErrorLog;
use App\Models\PublicacaoAto;
use App\Services\ErrorLogService;
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

            $publicacaos = PublicacaoAto::where('ativo', '=', PublicacaoAto::ATIVO)->get();

            return view('configuracao.publicacao-ato.index', compact('publicacaos'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PublicacaoAtoController', 'index');
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
    public function store(PublicacaoAtoRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('PublicacaoAto', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            PublicacaoAto::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            return redirect()->route('configuracao.publicacao_ato.index')->with('success', 'Cadastro realizado com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PublicacaoAtoController', 'store');
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

            $publicacao = PublicacaoAto::where('id', '=', $id)->where('ativo', '=', PublicacaoAto::ATIVO)->first();
            if (!$publicacao){
                return redirect()->route('configuracao.publicacao_ato.index')->with('erro', 'Não é possível alterar esta publicacao.');
            }

            return view('configuracao.publicacao-ato.edit', compact('publicacao'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PublicacaoAtoController', 'edit');
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
    public function update(PublicacaoAtoRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('PublicacaoAto', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $publicacao = PublicacaoAto::where('id', '=', $id)->where('ativo', '=', PublicacaoAto::ATIVO)->first();
            $publicacao->update($request->validated());

            return redirect()->route('configuracao.publicacao_ato.edit', $publicacao->id)->with('success', 'Alteração realizada com sucesso.');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PublicacaoAtoController', 'update');
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

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $publicacao = PublicacaoAto::where('id', '=', $id)->where('ativo', '=', PublicacaoAto::ATIVO)->first();
            if (!$publicacao){
                return redirect()->back()->with('erro', 'Não é possível excluir esta publicação.')->withInput();
            }

            $publicacao->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => PublicacaoAto::INATIVO
            ]);

            return redirect()->route('configuracao.publicacao_ato.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'PublicacaoAtoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
