<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReparticaoRequest;
use App\Models\Reparticao;
use App\Models\TipoReparticao;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ReparticaoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $reparticaos = Reparticao::where('ativo', '=', Reparticao::ATIVO)->get();

            return view('reparticao.index', compact('reparticaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $tipo_reparticaos = TipoReparticao::where('ativo', '=', TipoReparticao::ATIVO)->get();

            return view('reparticao.create', compact('tipo_reparticaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'create');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function store(ReparticaoRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            Reparticao::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            Alert::toast('Cadastro realizado com sucesso.','success');
            return redirect()->route('reparticao.index');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $reparticao = Reparticao::where('id', '=', $id)->where('ativo', '=', Reparticao::ATIVO)->first();
            if (!$reparticao){
                Alert::toast('Repartição inválida.','error');
                return redirect()->back();
            }

            $tipo_reparticaos = TipoReparticao::where('ativo', '=', TipoReparticao::ATIVO)->get();

            return view('reparticao.edit', compact('reparticao', 'tipo_reparticaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function update(ReparticaoRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $reparticao = Reparticao::where('id', '=', $id)->where('ativo', '=', Reparticao::ATIVO)->first();
            if (!$reparticao){
                Alert::toast('Repartição inválida.','error');
                return redirect()->back();
            }

            $reparticao->update($request->validated());

            Alert::toast('Alteração realizada com sucesso.','success');
            return redirect()->route('reparticao.edit', $reparticao->id);

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'update');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Reparticao', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $reparticao = Reparticao::where('id', '=', $id)->where('ativo', '=', Reparticao::ATIVO)->first();
            if (!$reparticao){
                Alert::toast('Repartição inválida.','error');
                return redirect()->back();
            }

            $reparticao->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => Reparticao::INATIVO,
            ]);

            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('reparticao.index');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
