<?php

namespace App\Http\Controllers;

use App\Models\AgentePolitico;
use App\Models\ErrorLog;
use App\Models\VereadorVotacao;
use App\Models\VotacaoEletronica;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VereadorVotacaoController extends Controller
{

    public function index() {

        $vereador = AgentePolitico::where('id_user', Auth::user()->id)->first();
        $vereador_votacaos = VereadorVotacao::where('id_vereador', $vereador->id)->where('ativo', 1)->get();

        // dd($votacaos);

        return view('votacao-eletronica.vereador.index', compact('vereador_votacaos'));
    }

    public function liberarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VereadorVotacao', 'Cadastro') != 1){
                return redirect()->back()->with('erro', 'Acesso negado.');
            }

            $vereadorVotacao = VereadorVotacao::where('id', '=', $id)->where('ativo', '=', 1)->first();
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
            $erro = new ErrorLog();
            $erro->erro = $ex->getMessage();
            $erro->controlador = "VereadorVotacaoController";
            $erro->funcao = "liberarVotacao";
            if (Auth::check()){
                $erro->cadastradoPorUsuario = auth()->user()->id;
            }
            $erro->save();
            return redirect()->back()->with('erro', 'Contate o administrador do sistema.');
        }
    }

    public function votacao($id) {

        $vereador = AgentePolitico::where('id_user', Auth::user()->id)->first();
        $votacao = VereadorVotacao::where('id', '=', $id)->where('id_vereador', $vereador->id)->where('ativo', '=', 1)->first();

        if (!$votacao){
            return redirect()->back()->with('erro', 'Dados inválidos.');
        }

        if($votacao->votacaoAutorizada != 1) {
            return redirect()->back()->with('erro', 'Votação não autorizada.');
        }


        return view('votacao-eletronica.vereador.votacao', compact('votacao'));


    }

    public function votar(Request $request, $id) {

        $vereador_votacao = VereadorVotacao::find($id);
        $vereador_votacao->votou = 1;
        $vereador_votacao->votouEm = Carbon::now();
        $vereador_votacao->save();
    }
}
