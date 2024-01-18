<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReparticaoRequest;
use App\Models\ErrorLog;
use App\Models\Reparticao;
use App\Models\TipoReparticao;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReparticaoController extends Controller
{
    public function index()
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $reparticaos = Reparticao::where('ativo', '=', Reparticao::ATIVO)->get();

            return view('reparticao.index', compact('reparticaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $tipo_reparticaos = TipoReparticao::where('ativo', '=', TipoReparticao::ATIVO)->get();

            return view('reparticao.create', compact('tipo_reparticaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(ReparticaoRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            Reparticao::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            return redirect()->route('reparticao.index')->with('success', 'Cadastro realizado com sucesso');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $reparticao = Reparticao::where('id', '=', $id)->where('ativo', '=', Reparticao::ATIVO)->first();
            if (!$reparticao){
                return redirect()->back()->with('erro', 'Repartição inválida.');
            }

            $tipo_reparticaos = TipoReparticao::where('ativo', '=', TipoReparticao::ATIVO)->get();

            return view('reparticao.edit', compact('reparticao', 'tipo_reparticaos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(ReparticaoRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('Reparticao', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $reparticao = Reparticao::where('id', '=', $id)->where('ativo', '=', Reparticao::ATIVO)->first();
            if (!$reparticao){
                return redirect()->back()->with('erro', 'Repartição inválida.');
            }

            $reparticao->update($request->validated());

            return redirect()->route('reparticao.edit', $reparticao->id)->with('success', 'Alteração realizada com sucesso');

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('Reparticao', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $reparticao = Reparticao::where('id', '=', $id)->where('ativo', '=', Reparticao::ATIVO)->first();
            if (!$reparticao){
                return redirect()->back()->with('erro', 'Repartição inválida.');
            }

            $reparticao->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => Reparticao::INATIVO,
            ]);

            return redirect()->route('reparticao.index')->with('success', 'Exclusão realizada com sucesso.');
            
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ReparticaoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }
}
