<?php

namespace App\Http\Controllers;

use App\Models\AgentePolitico;
use App\Models\ErrorLog;
use App\Models\VotacaoEletronica;
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

            // if ($votacao->votacaoIniciada != 1){
            //     $votacao->votacaoIniciada = 1;
            //     $votacao->dataHoraInicio = Carbon::now();
            //     $votacao->save();
            // }

            // $vereadores =
            // dd();
            $vereadores = AgentePolitico::leftJoin('pleito_eleitorals', 'pleito_eleitorals.id', '=', 'agente_politicos.id_pleito_eleitoral')
                ->where('pleito_eleitorals.id_legislatura', '=', $votacao->id_legislatura)
                ->where('agente_politicos.ativo', '=', 1)
                ->select('agente_politicos.*')
                ->get();

            return view('votacao-eletronica.gerenciamento.votacao', compact('votacao', 'vereadores'));
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
}
