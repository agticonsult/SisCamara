<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoAtoRequest;
use App\Models\TipoAto;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TipoAtoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('TipoAto', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $tipoAtos = TipoAto::where('ativo', '=', TipoAto::ATIVO)->get();

            return view('configuracao.tipo-ato.index', compact('tipoAtos'));
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'TipoAtoController', 'index');
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
    public function store(TipoAtoRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('TipoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            TipoAto::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            Alert::toast('Cadastro realizado com sucesso.','success');
            return redirect()->route('configuracao.tipo_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'TipoAtoController', 'store');
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
            if(Auth::user()->temPermissao('TipoAto', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $tipoAto = TipoAto::where('id', '=', $id)->where('ativo', '=', TipoAto::ATIVO)->first();
            if (!$tipoAto){
                Alert::toast('Não é possível alterar este tipo de ato.','error');
                return redirect()->route('configuracao.tipo_ato.index');
            }

            return view('configuracao.tipo-ato.edit', compact('tipoAto'));

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'TipoAtoController', 'edit');
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
    public function update(TipoAtoRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('TipoAto', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $tipoAto = TipoAto::where('id', '=', $id)->where('ativo', '=', TipoAto::ATIVO)->first();
            $tipoAto->update($request->validated());

            Alert::toast('Alteração realizada com sucesso.','success');
            return redirect()->route('configuracao.tipo_ato.index');

        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'TipoAtoController', 'update');
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
            if (Auth::user()->temPermissao('TipoAto', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;

            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $tipoAto = TipoAto::where('id', '=', $id)->where('ativo', '=', TipoAto::ATIVO)->first();
            if (!$tipoAto){
                Alert::toast('Não é possível excluir este tipo de ato.','error');
                return redirect()->back();
            }

            $tipoAto->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => TipoAto::ATIVO
            ]);

            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('configuracao.tipo_ato.index');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'TipoAtoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
