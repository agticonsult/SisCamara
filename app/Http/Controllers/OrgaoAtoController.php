<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrgaoAtoRequest;
use App\Models\OrgaoAto;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class OrgaoAtoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {/*
            if(Auth::user()->temPermissao('OrgaoAto', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $orgao_ato = OrgaoAto::where('ativo', '=', OrgaoAto::ATIVO)->get();

            return view('configuracao.orgao-ato.index', compact('orgao_ato'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'OrgaoAtoController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function create()
    {
        //
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrgaoAtoRequest $request)
    {
        try {/*
            if(Auth::user()->temPermissao('OrgaoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            OrgaoAto::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            Alert::toast('Cadastro realizado com sucesso.','success');
            return redirect()->route('configuracao.orgao_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'OrgaoAtoController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function show($id)
    {
        //
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {/*
            if(Auth::user()->temPermissao('OrgaoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $orgao_ato = OrgaoAto::where('id', '=', $id)->where('ativo', '=', OrgaoAto::ATIVO)->first();
            if (!$orgao_ato){
                Alert::toast('Não é possível alterar este assunto.','error');
                return redirect()->route('configuracao.orgao_ato.index');
            } else {
                return view('configuracao.orgao-ato.edit', compact('orgao_ato'));
            }
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'OrgaoAtoController', 'edit');
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
    public function update(OrgaoAtoRequest $request, $id)
    {
        try {/*
            if(Auth::user()->temPermissao('OrgaoAto', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $orgao_ato = OrgaoAto::where('id', '=', $id)->where('ativo', '=', OrgaoAto::ATIVO)->first();
            $orgao_ato->update($request->validated());

            Alert::toast('Alteração realizada com sucesso.','success');
            return redirect()->route('configuracao.orgao_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'OrgaoAtoController', 'update');
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
            if (Auth::user()->temPermissao('OrgaoAto', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }*/

            $orgao_ato = OrgaoAto::where('id', '=', $id)->where('ativo', '=', OrgaoAto::ATIVO)->first();
            if (!$orgao_ato){
                Alert::toast('Não é possível excluir este assunto.','error');
                return redirect()->back();
            } else {
                $orgao_ato->update([
                    'inativadoPorUsuario' => Auth::user()->id,
                    'dataInativado' => Carbon::now(),
                    'motivoInativado' => $request->motivo ?? "Exlcusão pelo usuário.",
                    'ativo' => OrgaoAto::INATIVO
                ]);

                Alert::toast('Exclusão realizada com sucesso.','success');
                return redirect()->route('configuracao.orgao_ato.index');
            }
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'OrgaoAtoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
