<?php

namespace App\Http\Controllers;

use App\Models\AgentePolitico;
use App\Models\VereadorVotacao;
use App\Services\ErrorLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class VereadorVotacaoController extends Controller
{

    public function index()
    {
        try {
            if(Auth::user()->temPermissao('VereadorVotacao', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $vereador = AgentePolitico::where('id_user', '=', Auth::user()->id)->first();
            if (!$vereador){
                Alert::toast('Vereador não cadastrado.','error');
                return redirect()->back();
            }

            $vereador_votacaos = VereadorVotacao::where('id_vereador', '=', $vereador->id)->where('ativo', VereadorVotacao::ATIVO)->get();

            return view('votacao-eletronica.vereador.index', compact('vereador_votacaos'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VereadorVotacaoController', 'index');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function liberarVotacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VereadorVotacao', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $vereadorVotacao = VereadorVotacao::where('id', '=', $id)->where('ativo', '=', VereadorVotacao::ATIVO)->first();
            if (!$vereadorVotacao){
                Alert::toast('Dados inválidos.','error');
                return redirect()->back();
            }

            if ($vereadorVotacao->votacaoIniciada != 1){
                $vereadorVotacao->votacaoAutorizada = 1;
                $vereadorVotacao->autorizadaPorUsuario = Auth::user()->id;
                $vereadorVotacao->autorizadaEm = Carbon::now();
                $vereadorVotacao->save();
            }

            Alert::toast('Votação do vereador iniciada com sucesso!','success');
            return redirect()->route('votacao_eletronica.gerenciamento.gerenciar', $vereadorVotacao->id_votacao);
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VereadorVotacaoController', 'liberarVotacao');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function votacao($id)
    {
        try {
            if(Auth::user()->temPermissao('VereadorVotacao', 'Listagem') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $vereador = AgentePolitico::where('id_user', Auth::user()->id)->first();
            $vereador_votacao = VereadorVotacao::where('id', '=', $id)->where('id_vereador', $vereador->id)->where('ativo', '=', VereadorVotacao::ATIVO)->first();

            if (!$vereador_votacao){
                Alert::toast('Dados inválidos','error');
                return redirect()->back();
            }

            if($vereador_votacao->votou == 1) {
                Alert::toast('Você já votou.','error');
                return redirect()->back();
            }

            if($vereador_votacao->votacaoAutorizada != 1) {
                Alert::toast('Votação não autorizada.','error');
                return redirect()->back();
            }

            return view('votacao-eletronica.vereador.votacao', compact('vereador_votacao'));

        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VereadorVotacaoController', 'votacao');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }

    public function votar(Request $request, $id)
    {
        try {
            if(Auth::user()->temPermissao('VereadorVotacao', 'Cadastro') != 1){
                Alert::toast('Acesso Negado!','error');
                return redirect()->back();
            }

            $vereador_votacao = VereadorVotacao::find($id);
            $vereador_votacao->votou = 1;
            $vereador_votacao->voto = $request->voto;
            $vereador_votacao->votouEm = Carbon::now();
            $vereador_votacao->save();

            Alert::toast('Sua votação foi registrada com sucesso!','success');
            return redirect()->route('votacao_eletronica.vereador.index');
        }
        catch (\Exception $ex) {
            ErrorLogService::salvar($ex->getMessage(), 'VereadorVotacaoController', 'votar');
            Alert::toast('Contate o administrador do sistema','error');
            return redirect()->back();
        }
    }
}
