<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicacaoAtoRequest;

use App\Models\PublicacaoAto;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

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
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $publicacaos = PublicacaoAto::where('ativo', '=', PublicacaoAto::ATIVO)->get();

            return view('configuracao.publicacao-ato.index', compact('publicacaos'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PublicacaoAtoController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
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
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            PublicacaoAto::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            Alert::toast('Cadastro realizada com sucesso.','success');
            return redirect()->route('configuracao.publicacao_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PublicacaoAtoController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
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
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $publicacao = PublicacaoAto::where('id', '=', $id)->where('ativo', '=', PublicacaoAto::ATIVO)->first();
            if (!$publicacao){
                Alert::toast('Não é possível alterar esta publicação.','error');
                return redirect()->back();
            }

            return view('configuracao.publicacao-ato.edit', compact('publicacao'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PublicacaoAtoController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
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
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $publicacao = PublicacaoAto::where('id', '=', $id)->where('ativo', '=', PublicacaoAto::ATIVO)->first();
            $publicacao->update($request->validated());

            Alert::toast('Alteração realizada com sucesso.','success');
            return redirect()->route('configuracao.publicacao_ato.edit', $publicacao->id);

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'PublicacaoAtoController', 'update');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
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
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $publicacao = PublicacaoAto::where('id', '=', $id)->where('ativo', '=', PublicacaoAto::ATIVO)->first();
            if (!$publicacao){
                Alert::toast('Não é possível excluir esta publicação.','error');
                return redirect()->back();
            }

            $publicacao->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => PublicacaoAto::INATIVO
            ]);
            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('configuracao.publicacao_ato.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'PublicacaoAtoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
