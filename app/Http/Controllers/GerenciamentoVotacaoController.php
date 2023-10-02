<?php

namespace App\Http\Controllers;

use App\Models\AgentePolitico;
use App\Models\ErrorLog;
use App\Models\VotacaoEletronica;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GerenciamentoVotacaoController extends Controller
{
    public function gerenciar($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            return view('votacao-eletronica.gerenciamento.votacao', compact('votacao'));
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "GerenciamentoVotacaoController";
            $erro->funcao = "edit";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function iniciarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            if ($votacao->votacaoIniciada != 1){
                $votacao->votacaoIniciada = 1;
                $votacao->dataHoraInicio = Carbon::now();
                $votacao->id_status_votacao = 2;
                $votacao->save();
            }

            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $id)->with('success', 'Votação iniciada com sucesso!');
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "GerenciamentoVotacaoController";
            $erro->funcao = "iniciarVotacao";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function pausarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            if ($votacao->votacaoPausada != 1){
                $qtdInterrupcao = $votacao->interrupcoes + 1;

                $votacao->votacaoPausada = 1;
                $votacao->interrupcoes = $qtdInterrupcao;
                $votacao->dataHoraInicio = Carbon::now();
                $votacao->id_status_votacao = 2;
                $votacao->save();
            }

            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $id)->with('success', 'Votação pausada com sucesso!');
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "GerenciamentoVotacaoController";
            $erro->funcao = "pausarVotacao";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function encerrarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', 1)->first();
            if (!$votacao){
                return redirect()->back()->with('erro', 'Votação inválida.');
            }

            if ($votacao->votacaoIniciada != 1){
                $votacao->votacaoIniciada = 1;
                $votacao->dataHoraInicio = Carbon::now();
                $votacao->id_status_votacao = 2;
                $votacao->save();
            }

            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $id)->with('success', 'Votação encerrada com sucesso!');
        }
        catch (\Exception $ex) {
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "GerenciamentoVotacaoController";
            $erro->funcao = "encerrarVotacao";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }
}
