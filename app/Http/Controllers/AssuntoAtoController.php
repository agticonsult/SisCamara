<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssuntoAtoRequest;
use App\Models\AssuntoAto;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class AssuntoAtoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('AssuntoAto', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $assuntoAtos = AssuntoAto::where('ativo', '=', AssuntoAto::ATIVO)->get();

            return view('configuracao.assunto-ato.index', compact('assuntoAtos'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'index');
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
    public function store(AssuntoAtoRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('AssuntoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            AssuntoAto::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            Alert::toast('Cadastro realizado com sucesso.','success');
            return redirect()->route('configuracao.assunto_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'store');
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
            if(Auth::user()->temPermissao('AssuntoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $assunto = AssuntoAto::where('id', '=', $id)->where('ativo', '=', AssuntoAto::ATIVO)->first();
            if (!$assunto){
                Alert::toast('Não é possível alterar este assunto.','error');
                return redirect()->route('configuracao.assunto_ato.index');
            }

            return view('configuracao.assunto-ato.edit', compact('assunto'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'edit');
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
    public function update(AssuntoAtoRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('AssuntoAto', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $assunto = AssuntoAto::where('id', '=', $id)->where('ativo', '=', AssuntoAto::ATIVO)->first();
            $assunto->update($request->validated());

            Alert::toast('Alteração realizada com sucesso.','success');
            return redirect()->route('configuracao.assunto_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'update');
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
            if (Auth::user()->temPermissao('AssuntoAto', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $assunto = AssuntoAto::where('id', '=', $id)->where('ativo', '=', AssuntoAto::ATIVO)->first();
            if (!$assunto){
                Alert::toast('Não é possível excluir este assunto.','error');
                return redirect()->back();
            }

            $assunto->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => AssuntoAto::INATIVO
            ]);

            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('configuracao.assunto_ato.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'AssuntoAtoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
