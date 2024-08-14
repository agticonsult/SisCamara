<?php

namespace App\Http\Controllers;

use App\Models\VotacaoEletronica;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class GerenciamentoVotacaoController extends Controller
{
    public function gerenciar($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                Alert::toast('Votação inválida.','error');
                return redirect()->back();
            }

            return view('votacao-eletronica.gerenciamento.votacao', compact('votacao'));
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'GerenciamentoVotacaoController', 'gerenciar');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function iniciarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                Alert::toast('Votação inválida.','error');
                return redirect()->back();
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

            Alert::toast('Votação iniciada com sucesso!','success');
            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $id);
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'GerenciamentoVotacaoController', 'iniciarVotacao');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function pausarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                Alert::toast('Votação inválida.','error');
                return redirect()->back();
            }

            if ($votacao->votacaoPausada != 1){
                $qtdInterrupcao = $votacao->interrupcoes + 1;

                $votacao->votacaoPausada = 1;
                $votacao->interrupcoes = $qtdInterrupcao;
                $votacao->dataHoraInicio = Carbon::now();
                $votacao->id_status_votacao = 5;
                $votacao->save();
            }

            Alert::toast('Votação pausada com sucesso!','success');
            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $id);
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'GerenciamentoVotacaoController', 'pausarVotacao');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function encerrarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VotacaoEletronica', 'Alteração') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $votacao = VotacaoEletronica::where('id', '=', $id)->where('ativo', '=', VotacaoEletronica::ATIVO)->first();
            if (!$votacao){
                Alert::toast('Votação inválida.','error');
                return redirect()->back();
            }

            $votacao->votacaoIniciada = 0;
            $votacao->votacaoEncerrada = 1;
            $votacao->dataHoraFim = Carbon::now();
            $votacao->inativadoPorUsuario = Auth::user()->id;
            $votacao->id_status_votacao = 4;
            // $votacao->ativo = 0;
            $votacao->save();

            Alert::toast('Votação encerrada com sucesso!','success');
            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $id);
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'GerenciamentoVotacaoController', 'encerrarVotacao');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
