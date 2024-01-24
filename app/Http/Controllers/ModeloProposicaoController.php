<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModeloProposicaoRequest;
use App\Models\ErrorLog;
use App\Models\ModeloProposicao;
use App\Services\ErrorLogService;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ModeloProposicaoController extends Controller
{
    use ApiResponser;

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelos = ModeloProposicao::where('ativo', '=', ModeloProposicao::ATIVO)->get();

            return view('proposicao.modelo.index', compact('modelos'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function create()
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            return view('proposicao.modelo.create');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'create');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function store(ModeloProposicaoRequest $request)
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            ModeloProposicao::create($request->validated() + [
                'cadastradoPorUsuario' => Auth::user()->id
            ]);

            return redirect()->route('proposicao.modelo.index')->with('success', 'Cadastro realizado com sucesso');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'store');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function edit($id)
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', ModeloProposicao::ATIVO)->first();
            if (!$modelo_proposicao){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            return view('proposicao.modelo.edit', compact('modelo_proposicao'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'edit');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function update(ModeloProposicaoRequest $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('ModeloProposicao', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', ModeloProposicao::ATIVO)->first();
            if (!$modelo_proposicao){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            $modelo_proposicao->update($request->validated());

            return redirect()->route('proposicao.modelo.edit', $modelo_proposicao->id)->with('success', 'Alteração realizada com sucesso');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'update');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            if (Auth::user()->temPermissao('ModeloProposicao', 'Exclusão') != 1) {
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $motivo = $request->motivo;
            if ($request->motivo == null || $request->motivo == "") {
                $motivo = "Exclusão pelo usuário.";
            }

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', ModeloProposicao::ATIVO)->first();
            if (!$modelo_proposicao){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            $modelo_proposicao->update([
                'inativadoPorUsuario' => Auth::user()->id,
                'dataInativado' => Carbon::now(),
                'motivoInativado' => $motivo,
                'ativo' => ModeloProposicao::INATIVO
            ]);

            return redirect()->route('proposicao.modelo.index')->with('success', 'Exclusão realizada com sucesso.');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'destroy');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.')->withInput();
        }
    }

    public function get($id)
    {
        try {
            if (Auth::user()->temPermissao('User', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $modelo_proposicao = ModeloProposicao::where('id', '=', $id)->where('ativo', '=', ModeloProposicao::ATIVO)->first();
            if (!$modelo_proposicao){
                return redirect()->back()->with('erro', 'Modelo inválido.');
            }

            return $this->success($modelo_proposicao);
        }
        catch(\Exception $ex){
            ErrorLogService::salvar($ex->getMessage(), 'ModeloProposicaoController', 'get');
            return $this->error('Erro, contate o administrador do sistema', 500);
        }
    }
}
