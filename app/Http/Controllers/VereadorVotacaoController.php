<?php

namespace App\Http\Controllers;

use App\Models\AgentePolitico;
use App\Models\ErrorLog;
use App\Models\VereadorVotacao;
use App\Models\VotacaoEletronica;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VereadorVotacaoController extends Controller
{

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('VereadorVotacao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $vereador = AgentePolitico::where('id_user', '=', Auth::user()->id)->first();

            if (!$vereador){
                return redirect()->back()->with('erro', 'Vereador não cadastrado.');
            }

            $vereador_votacaos = VereadorVotacao::where('id_vereador', '=', $vereador->id)->where('ativo', VereadorVotacao::ATIVO)->get();

            return view('votacao-eletronica.vereador.index', compact('vereador_votacaos'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VereadorVotacaoController', 'index');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function liberarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VereadorVotacao', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $vereadorVotacao = VereadorVotacao::where('id', '=', $id)->where('ativo', '=', VereadorVotacao::ATIVO)->first();
            if (!$vereadorVotacao){
                return redirect()->back()->with('erro', 'Dados inválidos.');
            }

            if ($vereadorVotacao->votacaoIniciada != 1){
                $vereadorVotacao->votacaoAutorizada = 1;
                $vereadorVotacao->autorizadaPorUsuario = Auth::user()->id;
                $vereadorVotacao->autorizadaEm = Carbon::now();
                $vereadorVotacao->save();
            }

            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $vereadorVotacao->id_votacao)->with('success', 'Votação do vereador iniciada com sucesso!');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VereadorVotacaoController', 'liberarVotacao');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function votacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VereadorVotacao', 'Listagem') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $vereador = AgentePolitico::where('id_user', Auth::user()->id)->first();
            $vereador_votacao = VereadorVotacao::where('id', '=', $id)->where('id_vereador', $vereador->id)->where('ativo', '=', VereadorVotacao::ATIVO)->first();

            if (!$vereador_votacao){
                return redirect()->back()->with('erro', 'Dados inválidos.');
            }

            if($vereador_votacao->votou == 1) {
                return redirect()->back()->with('erro', 'Você já votou.');
            }

            if($vereador_votacao->votacaoAutorizada != 1) {
                return redirect()->back()->with('erro', 'Votação não autorizada.');
            }

            return view('votacao-eletronica.vereador.votacao', compact('vereador_votacao'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VereadorVotacaoController', 'votacao');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function votar(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('VereadorVotacao', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $vereador_votacao = VereadorVotacao::find($id);
            $vereador_votacao->votou = 1;
            $vereador_votacao->voto = $request->voto;
            $vereador_votacao->votouEm = Carbon::now();
            $vereador_votacao->save();

            return redirect()->route('votacao_eletronica.vereador.index')->with('success', 'Sua votação foi registrada com sucesso!');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VereadorVotacaoController', 'votar');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
