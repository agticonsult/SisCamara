<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormaPublicacaoAtoRequest;
use App\Models\FormaPublicacaoAto;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class FormaPublicacaoAtoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {/*
            if(Auth::user()->temPermissao('AssuntoAto', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $forma_publicacao_atos = FormaPublicacaoAto::where('ativo', FormaPublicacaoAto::ATIVO)->get();

            return view('configuracao.forma-publi-ato.index', compact('forma_publicacao_atos'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'FormaPublicacaoAtoController', 'index');
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
    public function store(FormaPublicacaoAtoRequest $request)
    {
        try {/*
            if(Auth::user()->temPermissao('AssuntoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            FormaPublicacaoAto::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            Alert::toast('Cadastro realizado com sucesso.','success');
            return redirect()->route('configuracao.forma_publi_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'FormaPublicacaoAtoController', 'store');
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
            if(Auth::user()->temPermissao('AssuntoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $forma_publicacao_atos = FormaPublicacaoAto::where('ativo', FormaPublicacaoAto::ATIVO)->findOrFail($id);

            return view('configuracao.forma-publi-ato.edit', compact('forma_publicacao_atos'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'FormaPublicacaoAtoController', 'edit');
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
    public function update(FormaPublicacaoAtoRequest $request, $id)
    {
        try {/*
            if(Auth::user()->temPermissao('AssuntoAto', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $forma_publicacao_atos = FormaPublicacaoAto::where('id', $id)->where('ativo', FormaPublicacaoAto::ATIVO)->first();
            $forma_publicacao_atos->update($request->validated());

            Alert::toast('Alteração realizada com sucesso.','success');
            return redirect()->route('configuracao.forma_publi_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'FormaPublicacaoAtoController', 'update');
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
            if (Auth::user()->temPermissao('AssuntoAto', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $forma_publicacao_atos = FormaPublicacaoAto::where('id', $id)->where('ativo', FormaPublicacaoAto::ATIVO)->findOrFail($id);
            $forma_publicacao_atos->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $request->motivo ?? "Exclusão pelo usuário.",
                'ativo' => FormaPublicacaoAto::INATIVO
            ]);

            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('configuracao.forma_publi_ato.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'FormaPublicacaoAtoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
