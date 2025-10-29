<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassificacaoAtoRequest;
use App\Models\ClassificacaoAto;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ClassificacaoAtoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            /*if(Auth::user()->temPermissao('ClassificacaoAto', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $classificacao_ato = ClassificacaoAto::where('ativo', '=', ClassificacaoAto::ATIVO)->get();

            return view('configuracao.classificacao-ato.index', compact('classificacao_ato'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'ClassificacaoAtoController', 'index');
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
    public function store(ClassificacaoAtoRequest $request)
    {
        try {/*
            if(Auth::user()->temPermissao('ClassificacaoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            ClassificacaoAto::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            Alert::toast('Cadastro realizado com sucesso.','success');
            return redirect()->route('configuracao.classificacao_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'ClassificacaoAtoController', 'store');
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
        try {/*
            if(Auth::user()->temPermissao('ClassificacaoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $classificacao_ato = ClassificacaoAto::where('id', '=', $id)->where('ativo', '=', ClassificacaoAto::ATIVO)->first();
            if (!$classificacao_ato){
                Alert::toast('Não é possível alterar este assunto.','error');
                return redirect()->route('configuracao.classificacao_ato.index');
            } else {
                return view('configuracao.classificacao-ato.edit', compact('classificacao_ato'));
            }
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'CLassificacaoAtoController', 'edit');
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
    public function update(ClassificacaoAtoRequest $request, $id)
    {
        try {/*
            if(Auth::user()->temPermissao('ClassificacaoAto', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $classificacao_ato = ClassificacaoAto::where('id', '=', $id)->where('ativo', '=', ClassificacaoAto::ATIVO)->first();
            $classificacao_ato->update($request->validated());

            Alert::toast('Alteração realizada com sucesso.','success');
            return redirect()->route('configuracao.classificacao_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'ClassificacaoAtoController', 'update');
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
        try {/*
            if (Auth::user()->temPermissao('ClassificacaoAto', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $classificacao_ato = ClassificacaoAto::where('id', '=', $id)->where('ativo', '=', ClassificacaoAto::ATIVO)->first();
            if (!$classificacao_ato){
                Alert::toast('Não é possível excluir este assunto.','error');
                return redirect()->back();
            } else {
                $classificacao_ato->update([
                    'inativadoPorUsuario' => Auth::user()->id,
                    'dataInativado' => Carbon::now(),
                    'motivoInativado' => $request->motivo ?? "Exlcusão pelo usuário.",
                    'ativo' => ClassificacaoAto::INATIVO
                ]);

                Alert::toast('Exclusão realizada com sucesso.','success');
                return redirect()->route('configuracao.classificacao_ato.index');
            }
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ClassificacaoAtoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
