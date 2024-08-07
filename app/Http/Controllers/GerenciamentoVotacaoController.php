<?php

namespace App\Http\Controllers;

use App\Models\VotacaoEletronica;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GerenciamentoVotacaoController extends Controller
{
    public function gerenciar($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            return view('votacao-eletronica.gerenciamento.votacao', compact('votacao'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'GerenciamentoVotacaoController', 'gerenciar');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function iniciarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            if ($votacao->votacaoIniciada != 1){
                $votacao->votacaoIniciada = 1;
                $votacao->dataHoraInicio = Carbon::now();
                $votacao->id_status_votacao = 2;
                $votacao->save();
            }

            //se a votação estiver pausada
            if ($votacao->votacaoPausada == 1) {
                $votacao->votacaoPausada = 0;
                $votacao->id_status_votacao = 2;
                $votacao->save();
            }

            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $id)->with('success', 'Votação iniciada com sucesso!');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'GerenciamentoVotacaoController', 'iniciarVotacao');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function pausarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            if ($votacao->votacaoPausada != 1){
                $qtdInterrupcao = $votacao->interrupcoes + 1;

                $votacao->votacaoPausada = 1;
                $votacao->interrupcoes = $qtdInterrupcao;
                $votacao->dataHoraInicio = Carbon::now();
                $votacao->id_status_votacao = 5;
                $votacao->save();
            }

            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $id)->with('success', 'Votação pausada com sucesso!');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'GerenciamentoVotacaoController', 'pausarVotacao');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function encerrarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            $votacao->votacaoIniciada = 0;
            $votacao->votacaoEncerrada = 1;
            $votacao->dataHoraFim = Carbon::now();
            $votacao->inativadoPorUsuario = Auth::user()->id;
            $votacao->id_status_votacao = 4;
            // $votacao->ativo = 0;
            $votacao->save();

            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $id)->with('success', 'Votação encerrada com sucesso!');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'GerenciamentoVotacaoController', 'encerrarVotacao');
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
