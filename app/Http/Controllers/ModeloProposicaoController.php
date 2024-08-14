<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModeloProposicaoRequest;
use App\Models\ModeloProposicao;
use App\Services\ErrorLogService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ModeloProposicaoController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $modelos = ModeloProposicao::where('ativo', '=', ModeloProposicao::ATIVO)->get();

            return view('processo-legislativo.modelo.index', compact('modelos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            return view('processo-legislativo.modelo.create');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'create');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function store(ModeloProposicaoRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            ModeloProposicao::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);
            Alert::toast('Cadastro realizado com sucesso.','success');
            return redirect()->route('proposicao.modelo.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'store');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', ModeloProposicao::ATIVO)->first();
            if (!$modelo_proposicao){
                Alert::toast('Modelo inválido.','error');
                return redirect()->back();
            }

            return view('processo-legislativo.modelo.edit', compact('modelo_proposicao'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'edit');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function update(ModeloProposicaoRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', ModeloProposicao::ATIVO)->first();
            if (!$modelo_proposicao){
                Alert::toast('Modelo inválido.','error');
                return redirect()->back();
            }

            $modelo_proposicao->update($request->validated());
            Alert::toast('Alteração realizada com sucesso.','success');
            return redirect()->route('proposicao.modelo.edit', $modelo_proposicao->id);
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'update');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('ModeloProposicao', 'Exclusão') != 1) {
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', ModeloProposicao::ATIVO)->first();
            if (!$modelo_proposicao){
                Alert::toast('Modelo inválido.','error');
                return redirect()->back();
            }

            $modelo_proposicao->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => ModeloProposicao::INATIVO
            ]);
            Alert::toast('Exclusão realizada com sucesso.','success');
            return redirect()->route('proposicao.modelo.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'destroy');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function get($id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', ModeloProposicao::ATIVO)->first();
            if (!$modelo_proposicao){
                Alert::toast('Modelo inválido.','error');
                return redirect()->back();
            }

            return $this->success($modelo_proposicao);
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'get');
            return $this->error('Erro, contate o administrador do sistema', 500);
        }
    }
}
